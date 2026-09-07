const {chromium}=require(process.env.PLAYWRIGHT_MODULE);
const fs=require('fs');
const path=require('path');
(async()=>{
 const out=process.env.OC_AUDIT_OUTPUT;
 fs.mkdirSync(out,{recursive:true});
 const browser=await chromium.launch({headless:true,executablePath:process.env.BROWSER_EXE});
 try {
  const page=await browser.newPage(); const results=[];
  for(const width of [1440,390,320]){
   await page.setViewportSize({width,height:1000});
   await page.goto('http://localhost:8091/',{waitUntil:'load'});
   const table=page.locator('.wp-block-table');
   const result=await table.evaluate(el=>({viewport:innerWidth,scroll:document.documentElement.scrollWidth,tableWidth:el.getBoundingClientRect().width,tableScroll:el.scrollWidth,headers:[...el.querySelectorAll('th')].map(th=>{const r=document.createRange();r.selectNodeContents(th);return {text:th.textContent,lines:r.getClientRects().length,minWidth:getComputedStyle(th).minWidth,padding:getComputedStyle(th).padding}})}));
   if(result.viewport!==result.scroll||result.tableScroll>result.tableWidth+1||result.headers.some(h=>h.lines!==1))throw new Error(JSON.stringify(result));
   await table.screenshot({path:path.join(out,`table-${width}.jpg`),type:'jpeg'});
   // Stress only the live DOM: no saved fixture/template is changed.
   await table.evaluate(el=>{const hs=el.querySelectorAll('th');hs[0].textContent='会社名';hs[1].textContent='所在地';hs[2].textContent='事業内容';el.querySelector('td').textContent='地域の仕事を支える技術とサービス Example Studio / '+ 'LongUnbrokenBusinessName'.repeat(8);});
   const stress=await table.evaluate(el=>({viewport:innerWidth,scroll:document.documentElement.scrollWidth,tableWidth:el.getBoundingClientRect().width,tableScroll:el.scrollWidth}));
   if(stress.viewport!==stress.scroll||stress.tableScroll>stress.tableWidth+1)throw new Error(JSON.stringify(stress));
   await table.screenshot({path:path.join(out,`table-ja-stress-${width}.jpg`),type:'jpeg'});
   results.push({normal:result,japaneseLongValue:stress});
  }
  fs.writeFileSync(path.join(out,'table-results.json'),JSON.stringify(results,null,2));
  console.log(JSON.stringify(results));
 }finally{await browser.close();}
})().catch(e=>{console.error(e);process.exit(1)});
