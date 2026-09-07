const {chromium}=require(process.env.PLAYWRIGHT_MODULE);
const fs=require('fs');const path=require('path');
(async()=>{
 const out=process.env.OC_AUDIT_OUTPUT;fs.mkdirSync(out,{recursive:true});
 const browser=await chromium.launch({headless:true,executablePath:process.env.BROWSER_EXE});
 try{
  const page=await browser.newPage({viewport:{width:1440,height:1000}});
  await page.goto('http://localhost:8091/wp-login.php');
  await page.locator('#user_login').fill('starter_review');
  await page.locator('#user_pass').fill(process.env.OC_STARTER_PASSWORD);
  await page.locator('#wp-submit').click();await page.waitForURL('**/wp-admin/**');
  await page.goto('http://localhost:8091/wp-admin/themes.php',{waitUntil:'load'});
  await page.screenshot({path:path.join(out,'theme-list.jpg'),type:'jpeg',quality:75});
  await page.goto('http://localhost:8091/wp-admin/themes.php?theme=ozeki-corporate',{waitUntil:'load'});
  await page.getByRole('heading',{name:'Ozeki Corporate',exact:true}).waitFor();
  await page.screenshot({path:path.join(out,'theme-details.jpg'),type:'jpeg',quality:75});
  console.log((await page.locator('body').innerText()).slice(-7000));
 }finally{await browser.close();}
})().catch(e=>{console.error(e);process.exit(1)});
