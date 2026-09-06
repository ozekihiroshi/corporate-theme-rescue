const {chromium}=require(process.env.PLAYWRIGHT_MODULE || 'playwright');
(async()=>{
 const output=process.argv[2]; if(!output) throw new Error('Supply output screenshot.png path');
 const browser=await chromium.launch({headless:true,...(process.env.BROWSER_EXE?{executablePath:process.env.BROWSER_EXE}:{})});
 const page=await browser.newPage({viewport:{width:1200,height:900},deviceScaleFactor:1});
 await page.goto('http://localhost:8089/',{waitUntil:'networkidle'});
 await page.screenshot({path:output});
 await browser.close();
})().catch(e=>{console.error(e);process.exit(1)});
