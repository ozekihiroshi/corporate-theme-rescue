const {chromium}=require(process.env.PLAYWRIGHT_MODULE||'playwright');
const fs=require('fs');const path=require('path');
(async()=>{
 const id=Number(process.env.OC_FIXTURE_ID);if(!id||!process.env.OC_ZIPTEST_PASSWORD)throw new Error('Set fixture ID and test account password');
 const browser=await chromium.launch({headless:true,executablePath:process.env.BROWSER_EXE});
 const page=await browser.newPage({viewport:{width:1440,height:1000}});
 await page.goto('http://localhost:8090/wp-login.php',{waitUntil:'load'});
 await page.locator('#user_login').fill('theme_ziptest');
 await page.locator('#user_pass').fill(process.env.OC_ZIPTEST_PASSWORD);
 await page.locator('#wp-submit').click();
 await page.goto(`http://localhost:8090/wp-admin/post.php?post=${id}&action=edit`,{waitUntil:'domcontentloaded',timeout:60000});
 await page.waitForFunction(()=>window.wp?.data?.select('core/editor')?.getCurrentPostId());
 const before=await page.evaluate(async id=>{
  const p=await wp.apiFetch({path:`/wp/v2/pages/${id}?context=edit`});
  if(!p.slug.startsWith('oc-editor-audit-'))throw new Error('Not an audit fixture');
  const invalid=[];const walk=bs=>bs.forEach(b=>{if(!b.isValid)invalid.push(b.name);walk(b.innerBlocks||[])});walk(wp.blocks.parse(p.content.raw));
  return {content:p.content.raw,invalid};
 },id);
 if(before.invalid.length)throw new Error('Invalid fixture blocks: '+before.invalid.join(','));
 // Exercise the editor data store and its normal save pipeline without changing theme settings.
 const after=await page.evaluate(async ({id,content})=>{
  const edit=content.replace('Revision baseline A.','Editor roundtrip C.');
  wp.data.dispatch('core/editor').editPost({content:edit});
  await wp.data.dispatch('core/editor').savePost();
  const saved=await wp.apiFetch({path:`/wp/v2/pages/${id}?context=edit`});
  if(saved.content.raw!==edit)throw new Error('Editor save mismatch');
  return {saved:true,content:edit};
 },{id,content:before.content});
 await page.reload({waitUntil:'domcontentloaded'});
 await page.waitForFunction(()=>window.wp?.data?.select('core/editor')?.getCurrentPostId());
 const reopened=await page.evaluate(()=>wp.data.select('core/editor').getEditedPostContent());
 if(reopened!==after.content)throw new Error('Reopened content differs');
 await page.evaluate(async ({id,content})=>{wp.data.dispatch('core/editor').editPost({content});await wp.data.dispatch('core/editor').savePost();const p=await wp.apiFetch({path:`/wp/v2/pages/${id}?context=edit`});if(p.content.raw!==content)throw new Error('Baseline restore mismatch')},{id,content:before.content});
 const result={id,invalidBlocks:before.invalid,editorSave:true,reopen:true,baselineRestored:true,method:'Gutenberg editor data store and savePost; no synthetic typing'};
 fs.writeFileSync(path.join(process.env.OC_AUDIT_OUTPUT,'editor-roundtrip.json'),JSON.stringify(result,null,2));
 console.log(JSON.stringify(result));await browser.close();
})().catch(e=>{console.error(e);process.exit(1)});
