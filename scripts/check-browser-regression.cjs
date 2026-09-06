const {chromium,firefox}=require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const fs=require('fs');
const path=require('path');
const out=process.env.OC_AUDIT_OUTPUT;
if(!out) throw new Error('Set OC_AUDIT_OUTPUT to an evidence directory');
fs.mkdirSync(out,{recursive:true});
(async()=>{
 const results=[];
 for(const name of (process.env.OC_BROWSERS||'edge,firefox').split(',')){
  const browser=await (name==='edge'?chromium:firefox).launch({headless:true,...(name==='edge'?{executablePath:process.env.BROWSER_EXE}:{})});
  const page=await browser.newPage();
  try {
  const errors=[];page.on('pageerror',e=>errors.push(String(e)));
  for(const width of [1440,390,320]){
   await page.setViewportSize({width,height:1000});
   for(const url of [...(process.env.OC_QUICK?[]:['http://localhost:8089/','http://localhost:8089/design-guide/','http://localhost:8089/english/','http://localhost:8090/?s=test']),'http://localhost:8090/',...(process.env.OC_FIXTURE_URL?[process.env.OC_FIXTURE_URL]:[])]){
    const response=await page.goto(url,{waitUntil:'load',timeout:60000});
    const layout=await page.evaluate(()=>({width:innerWidth,scroll:document.documentElement.scrollWidth,brokenImages:[...document.images].filter(i=>!i.complete||!i.naturalWidth).map(i=>i.src),headings:[...document.querySelectorAll('h1')].map(e=>e.textContent)}));
    results.push({browser:name,url,status:response.status(),...layout});
   }
   await page.goto('http://localhost:8090/',{waitUntil:'load',timeout:60000});
   if(width===1440){
    const toggle=page.getByRole('button',{name:'Pages submenu',exact:true});
    await toggle.focus();await page.keyboard.press('Enter');
    results.push({browser:name,desktopSubmenuExpanded:await toggle.getAttribute('aria-expanded')});
    await page.keyboard.press('Escape');
   }else{
    const menu=page.locator('.wp-block-navigation__responsive-container-open');
    await menu.focus();await page.keyboard.press('Enter');
    const opened=await page.locator('.is-menu-open').count()===1;
    await page.keyboard.press('Escape');
    results.push({browser:name,width,opened,closed:await page.locator('.is-menu-open').count()===0,focusReturned:await menu.evaluate(el=>el===document.activeElement)});
   }
  }
  await page.setViewportSize({width:1200,height:900});
  await page.goto('http://localhost:8090/',{waitUntil:'load',timeout:60000});
  const hero=await page.locator('.wp-block-cover').evaluate(el=>({background:getComputedStyle(el).backgroundColor,overlayColor:getComputedStyle(el.querySelector('.wp-block-cover__background')).backgroundColor,overlayOpacity:getComputedStyle(el.querySelector('.wp-block-cover__background')).opacity,text:[...el.querySelectorAll('p,h1')].map(p=>({text:p.textContent,color:getComputedStyle(p).color,size:getComputedStyle(p).fontSize})),body:getComputedStyle(document.body).backgroundColor}));
  results.push({browser:name,hero,errors});
  await page.screenshot({path:path.join(out,`${name}-default-hero.jpg`),type:'jpeg',quality:70});
  } finally {
   fs.writeFileSync(path.join(out,'browser-regression.json'),JSON.stringify(results,null,2));
   await browser.close();
  }
 }
 fs.writeFileSync(path.join(out,'browser-regression.json'),JSON.stringify(results,null,2));
 console.log(JSON.stringify(results,null,2));
 if(results.some(r=>r.scroll>r.width || r.brokenImages?.length || r.errors?.length || r.desktopSubmenuExpanded==='false' || r.closed===false || r.opened===false || r.focusReturned===false))process.exitCode=1;
})().catch(e=>{console.error(e);process.exit(1)});
