const {chromium}=require(process.env.PLAYWRIGHT_MODULE);
const fs=require('fs'); const path=require('path');
(async()=>{
 const out=process.env.OC_AUDIT_OUTPUT; fs.mkdirSync(out,{recursive:true});
 const browser=await chromium.launch({headless:true,executablePath:process.env.BROWSER_EXE});
 try {
  const page=await browser.newPage({viewport:{width:1440,height:1000}});
  await page.goto('http://localhost:8091/wp-login.php');
  await page.locator('#user_login').fill(process.env.OC_GUIDE_USER);
  await page.locator('#user_pass').fill(process.env.OC_STARTER_PASSWORD);
  await page.locator('#wp-submit').click(); await page.waitForURL('**/wp-admin/**');
  await page.goto('http://localhost:8091/wp-admin/themes.php?page=ozeki-corporate-guide');
  await page.getByRole('heading',{name:'Make this website your own',exact:true}).waitFor();
  if(!await page.getByText('Use Pages for About',{exact:false}).count()) throw Error('English guide missing');
  const write=page.getByRole('link',{name:'Write a news post',exact:true});
  if(await write.getAttribute('href')!=='http://localhost:8091/wp-admin/post-new.php') throw Error('Wrong post link');
  await page.screenshot({path:path.join(out,'guide-en.png'),fullPage:true});
  await page.getByRole('link',{name:'日本語の操作ガイド',exact:true}).click();
  if(!await page.getByText('投稿と固定ページ：',{exact:false}).count()) throw Error('Japanese guide missing');
  await page.screenshot({path:path.join(out,'guide-ja.png')});
  const guide=await page.getByRole('link',{name:'Read the complete English / Japanese guide (Markdown)',exact:true}).getAttribute('href');
  const response=await page.request.get(guide); const body=await response.text();
  if(!response.ok()||!body.includes('### Pages or posts?')||!body.includes('### 固定ページと投稿を使い分ける')) throw Error('Packaged guide missing');
  await page.getByRole('link',{name:'Review posts',exact:true}).click();
  await page.waitForURL('**/wp-admin/edit.php'); await page.locator('#posts-filter').waitFor();
  fs.writeFileSync(path.join(out,'guide-results.json'),JSON.stringify({result:'pass',guide,writePostLink:'verified href only',reviewPosts:'opened',languageAnchor:'opened',contentCreated:false},null,2));
  console.log('GUIDE_UI_PASS');
 } finally {await browser.close();}
})().catch(e=>{console.error(e);process.exit(1)});
