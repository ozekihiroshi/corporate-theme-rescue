const {chromium}=require(process.env.PLAYWRIGHT_MODULE);
const fs=require('fs');
const path=require('path');
(async()=>{
 const out=process.env.OC_AUDIT_OUTPUT;
 fs.mkdirSync(out,{recursive:true});
 const browser=await chromium.launch({headless:true,executablePath:process.env.BROWSER_EXE});
 try {
 const page=await browser.newPage();
 const results=[];
 for(const width of (process.env.OC_VERIFY_ONLY?[]:[1440,390,320])){
   await page.setViewportSize({width,height:1000});
   await page.goto('http://localhost:8091/',{waitUntil:'load',timeout:60000});
   results.push(await page.evaluate(()=>({width:innerWidth,scroll:document.documentElement.scrollWidth,images:[...document.images].map(i=>({src:i.src,loaded:i.complete&&i.naturalWidth>0})),h1:[...document.querySelectorAll('h1')].map(e=>e.textContent)})));
   await page.screenshot({path:path.join(out,`initial-${width}.jpg`),type:'jpeg',quality:65,fullPage:true});
 }
 await page.setViewportSize({width:1440,height:1000});
 await page.goto('http://localhost:8091/wp-login.php');
 await page.locator('#user_login').fill('starter_review');
 await page.locator('#user_pass').fill(process.env.OC_STARTER_PASSWORD);
 await page.locator('#wp-submit').click();
 await page.waitForURL('**/wp-admin/**');
 await page.goto('http://localhost:8091/wp-admin/site-editor.php?postId=ozeki-corporate%2F%2Ffront-page&postType=wp_template&canvas=edit');
 await page.waitForTimeout(5000);
 if(await page.getByRole('button',{name:'Get started',exact:true}).count()) await page.getByRole('button',{name:'Get started',exact:true}).click();
 if(!process.env.OC_VERIFY_ONLY) fs.writeFileSync(path.join(out,'initial-results.json'),JSON.stringify(results,null,2));
 await page.screenshot({path:path.join(out,'editor-initial.png')});
 console.log(JSON.stringify(results));
 console.log((await page.locator('body').innerText()).slice(0,10000));
 console.log('FRAMES',page.frames().map(f=>f.url()));
 const canvas=page.frames().find(f=>f!==page.mainFrame());
 if(process.env.OC_VERIFY_ONLY){
   console.log('REOPENED',await canvas.locator('body').ariaSnapshot());
   if(await canvas.getByRole('document',{name:'Block: Heading 1',exact:true}).innerText()!=='Clear advice. Practical progress.') throw new Error('Heading not persisted');
   if(!(await canvas.locator('body').innerText()).includes('地域の仕事に、確かな一歩を。')) throw new Error('Mixed language text not persisted');
   const uploaded=canvas.getByRole('document',{name:'Block: Image',exact:true});
   if(!(await uploaded.locator('img').getAttribute('src')).includes('/uploads/')) throw new Error('Image not persisted');
   await uploaded.click();
   await page.getByRole('textbox',{name:'Alternative text',exact:true}).fill('Test illustration of monitoring equipment on a desk');
 }else{
 await canvas.getByRole('document',{name:'Block: Heading 1',exact:true}).fill('Clear advice. Practical progress.');
 await canvas.getByRole('document',{name:'Block: Paragraph',exact:true}).filter({hasText:'We help people turn everyday challenges into workable improvements.'}).fill('地域の仕事に、確かな一歩を。 Practical support for your next step.');
 await canvas.getByRole('document',{name:'Block: Image',exact:true}).click();
 await page.getByRole('button',{name:'Replace',exact:true}).click();
 console.log('REPLACE',await page.locator('body').ariaSnapshot());
 const chooserPromise=page.waitForEvent('filechooser');
 await page.getByRole('menuitem',{name:'Upload',exact:true}).click();
 await (await chooserPromise).setFiles(path.join(__dirname,'assets','monitoring.png'));
 await page.getByRole('textbox',{name:'Alternative text',exact:true}).fill('Test illustration of monitoring equipment on a desk');
 await page.waitForFunction(()=>[...document.querySelectorAll('iframe')].some(f=>f.contentDocument&&[...f.contentDocument.images].some(i=>i.src.includes('/uploads/')&&i.complete&&i.naturalWidth>0)),{},{timeout:60000});
 await page.getByRole('textbox',{name:'Alternative text',exact:true}).fill('Test illustration of monitoring equipment on a desk');
 }
 await page.getByRole('button',{name:'Save',exact:true}).click();
 await page.waitForFunction(()=>!document.body.innerText.includes('Saving…'),{},{timeout:30000});
 await page.reload({waitUntil:'load'});
 await page.waitForTimeout(3000);
 const reopened=page.frames().find(f=>f!==page.mainFrame());
 const contents=await reopened.locator('body').innerText();
 const image=reopened.getByRole('document',{name:'Block: Image',exact:true}).locator('img');
 if(!contents.includes('Clear advice. Practical progress.')||!contents.includes('地域の仕事に、確かな一歩を。')) throw new Error('Reopened text mismatch');
 if((await image.getAttribute('alt'))!=='Test illustration of monitoring equipment on a desk') throw new Error('Reopened alternative text mismatch');
 if(!((await image.getAttribute('src'))||'').includes('/uploads/')) throw new Error('Reopened image mismatch');
 if((await page.locator('body').innerText()).includes('Attempt recovery')||contents.includes('Attempt recovery')) throw new Error('Block recovery required');
 await page.screenshot({path:path.join(out,'editor-reopened.jpg'),type:'jpeg',quality:65});
 const finished=[];
 for(const width of [1440,390,320]){
   await page.setViewportSize({width,height:1000});
   await page.goto('http://localhost:8091/',{waitUntil:'load'});
   const observed=await page.evaluate(()=>({width:innerWidth,scroll:document.documentElement.scrollWidth,heading:document.querySelector('h1')?.textContent,text:document.body.innerText.includes('地域の仕事に、確かな一歩を。'),image:[...document.images].find(i=>i.src.includes('/uploads/'))?.src}));
   if(observed.heading!=='Clear advice. Practical progress.'||!observed.text||!observed.image||observed.width!==observed.scroll) throw new Error('Public result mismatch');
   finished.push(observed);
   await page.screenshot({path:path.join(out,`edited-${width}.jpg`),type:'jpeg',quality:65,fullPage:true});
 }
 fs.writeFileSync(path.join(out,'edit-results.json'),JSON.stringify({method:'UI contenteditable fill, Replace > Upload file chooser, alternative text field, Save, reload',reopened:true,public:finished},null,2));
 console.log('EDIT_SAVE_REOPEN_PASS',JSON.stringify(finished));
 } finally {await browser.close();}
})().catch(e=>{console.error(e);process.exit(1)});
