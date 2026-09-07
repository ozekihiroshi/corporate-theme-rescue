const {chromium}=require(process.env.PLAYWRIGHT_MODULE);
const fs=require('fs');const path=require('path');
(async()=>{
 const out=process.env.OC_AUDIT_OUTPUT;fs.mkdirSync(out,{recursive:true});
 const browser=await chromium.launch({headless:true,executablePath:process.env.BROWSER_EXE});
 try {
 const page=await browser.newPage({viewport:{width:1440,height:1000}});
 await page.goto('http://localhost:8091/wp-login.php');
 await page.locator('#user_login').fill('starter_review');
 await page.locator('#user_pass').fill(process.env.OC_STARTER_PASSWORD);
 await page.locator('#wp-submit').click();await page.waitForURL('**/wp-admin/**');
 await page.goto('http://localhost:8091/wp-admin/themes.php');
 await page.getByRole('link',{name:'Ozeki Corporate Guide',exact:true}).click();
 await page.getByRole('heading',{name:'Make this website your own',exact:true}).waitFor();
 await page.screenshot({path:path.join(out,'guide-en.jpg'),type:'jpeg',quality:70});
 await page.getByRole('link',{name:'日本語の操作ガイド',exact:true}).click();
 await page.screenshot({path:path.join(out,'guide-ja.jpg'),type:'jpeg',quality:70});
 const guideLinks=await page.locator('.wrap a').evaluateAll(as=>as.map(a=>({text:a.textContent,url:a.href})));
 if(guideLinks.some(a=>!a.url.startsWith('http://localhost:8091/')))throw new Error('Unexpected external guide link');
 await page.getByRole('link',{name:'Add a page',exact:true}).click();
 await page.waitForFunction(()=>window.wp?.blocks?.parse,{},{timeout:60000});
 const patterns=await page.evaluate(async()=>{
  const ps=await wp.apiFetch({path:'/wp/v2/block-patterns/patterns'});
  return ps.filter(p=>p.name.startsWith('ozeki-corporate/page-')).map(p=>{
   const invalid=[];const walk=bs=>bs.forEach(b=>{if(!b.isValid)invalid.push(b.name);walk(b.innerBlocks||[])});walk(wp.blocks.parse(p.content));
   return {name:p.name,title:p.title,invalid,blockTypes:p.blockTypes,postTypes:p.postTypes};
  });
 });
 if(patterns.length!==4||patterns.some(p=>p.invalid.length))throw new Error(JSON.stringify(patterns));
 fs.writeFileSync(path.join(out,'pattern-validation.json'),JSON.stringify({guideLinks,patterns},null,2));
 await page.waitForTimeout(3000);
 await page.screenshot({path:path.join(out,'page-chooser.jpg'),type:'jpeg',quality:70});
 await page.getByText('Ozeki Corporate — About',{exact:true}).click();
 const editor=page.frameLocator('iframe[name="editor-canvas"]');
 await editor.getByRole('textbox',{name:'Add title'}).fill('Onboarding check – About');
 await page.getByRole('button',{name:'Save draft',exact:true}).click();
 await page.waitForFunction(()=>wp.data.select('core/editor').isEditedPostDirty()===false);
 const id=await page.evaluate(()=>wp.data.select('core/editor').getCurrentPostId());
 await page.goto(`http://localhost:8091/wp-admin/post.php?post=${id}&action=edit`);
 await page.waitForFunction(()=>window.wp?.data?.select('core/editor')?.getCurrentPost()?.id);
 const saved=await page.evaluate(()=>{const p=wp.data.select('core/editor').getCurrentPost();return {id:p.id,status:p.status,title:p.title,content:p.content};});
 if(saved.status!=='draft'||!JSON.stringify(saved.content).includes('Good work begins with a conversation.'))throw new Error('Draft content did not survive reload');
 fs.writeFileSync(path.join(out,'saved-draft.json'),JSON.stringify(saved,null,2));
 await page.screenshot({path:path.join(out,'saved-draft.jpg'),type:'jpeg',quality:70});
 console.log(JSON.stringify({result:'pass',draftId:id,patterns:patterns.length}));
 }finally{await browser.close();}
})().catch(e=>{console.error(e);process.exit(1)});
