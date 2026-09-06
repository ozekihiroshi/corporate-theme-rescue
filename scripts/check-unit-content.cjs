const {chromium,webkit}=require(process.env.PLAYWRIGHT_MODULE);
const fs=require('fs');
const path=require('path');
const out=process.env.OC_AUDIT_OUTPUT;
fs.mkdirSync(out,{recursive:true});
(async()=>{
 const results=[];
 for(const name of ['edge','webkit']){
  const browser=await (name==='edge'?chromium:webkit).launch({headless:true,...(name==='edge'?{executablePath:process.env.BROWSER_EXE}:{})});
  try {
   const page=await browser.newPage();
   const errors=[]; page.on('pageerror',e=>errors.push(String(e)));
   for(const width of [1440,320]){
    await page.setViewportSize({width,height:1000});
    for(const id of (process.env.OC_POST_IDS||'163,150,51,34,24,21,164,1724,501,555,1787,1788').split(',').map(Number)){
     const response=await page.goto(`http://localhost:8090/?p=${id}`,{waitUntil:'domcontentloaded',timeout:60000});
     await page.waitForTimeout(500);
     const state=await page.evaluate(()=>({width:innerWidth,scroll:document.documentElement.scrollWidth,title:document.title,main:document.querySelectorAll('main').length,brokenImages:[...document.images].filter(i=>i.complete&&!i.naturalWidth).map(i=>i.src),overflow:[...document.querySelectorAll('main *')].filter(e=>e.getBoundingClientRect().right>innerWidth+2).slice(0,8).map(e=>({tag:e.tagName,class:e.className}))}));
     results.push({browser:name,id,status:response.status(),...state});
    }
   }
   await page.goto('http://localhost:8090/',{waitUntil:'domcontentloaded'});
   fs.writeFileSync(path.join(out,`${name}-aria.txt`),await page.locator('body').ariaSnapshot());
   const landmarks=await page.evaluate(()=>({headers:document.querySelectorAll('header header').length,footers:document.querySelectorAll('footer footer').length}));
   results.push({browser:name,errors,landmarks});
  } finally {fs.writeFileSync(path.join(out,'unit-content.json'),JSON.stringify(results,null,2)); await browser.close();}
 }
 console.log(JSON.stringify({checks:results.filter(r=>r.id).length,findings:results.filter(r=>r.scroll>r.width||r.status>=400||r.brokenImages?.length||r.errors?.length)},null,2));
 if(results.some(r=>r.scroll>r.width||r.status>=400||r.brokenImages?.length||r.errors?.length||r.landmarks?.headers||r.landmarks?.footers))process.exitCode=1;
})().catch(e=>{console.error(e);process.exitCode=1});
