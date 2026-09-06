// Run with Playwright and axe-core installed as development tools, not theme assets.
const {chromium}=require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const axe=process.env.AXE_SOURCE || require.resolve('axe-core/axe.min.js');
(async()=>{
 const browser=await chromium.launch({headless:true,...(process.env.BROWSER_EXE?{executablePath:process.env.BROWSER_EXE}:{})});
 const page=await browser.newPage();
 const results=[];
 for(const base of ['http://localhost:8089','http://localhost:8090']){
  for(const width of [1440,390]){
   await page.setViewportSize({width,height:1000});
   for(const slug of (base.endsWith('8089')?['/','/about/','/services/','/company/','/contact/','/english/','/design-guide/','/news/','/?s=Energy','/demo-page-not-found/']:['/','/?s=test','/?page_id=2'])){
    await page.goto(base+slug,{waitUntil:'load',timeout:60000});
    await page.addScriptTag({path:axe});
    const scan=await page.evaluate(async()=>{
     const r=await axe.run(document,{runOnly:{type:'tag',values:['wcag2a','wcag2aa','wcag21aa']}});
     return {violations:r.violations.map(v=>({id:v.id,impact:v.impact,description:v.description,nodes:v.nodes.map(n=>({target:n.target,summary:n.failureSummary}))})),incomplete:r.incomplete.map(v=>v.id)};
    });
    results.push({base,slug,width,...scan});
   }
  }
  await page.goto(base+'/',{waitUntil:'load',timeout:60000});
  await page.keyboard.press('Tab');
  const skip=await page.evaluate(()=>({text:document.activeElement.textContent,href:document.activeElement.getAttribute('href'),rect:document.activeElement.getBoundingClientRect().toJSON()}));
  let target=null;
  if(skip.href?.startsWith('#')) {
   await page.keyboard.press('Enter');
   target=await page.evaluate(()=>({tag:document.activeElement.tagName,id:document.activeElement.id}));
  }
  await page.keyboard.press('Tab');
  const nextFocusInMain=await page.evaluate(()=>!!document.activeElement.closest('main'));
  const menu=page.locator('.wp-block-navigation__responsive-container-open');
  await menu.focus(); await page.keyboard.press('Enter');
  const opened=await page.locator('.is-menu-open').count();
  const submenu=page.locator('.is-menu-open .wp-block-navigation-submenu__toggle').first();
  let submenuExpanded=null;
  if(await submenu.count() && await submenu.isVisible()) {
   await submenu.focus(); await page.keyboard.press('Enter');
   submenuExpanded=await submenu.getAttribute('aria-expanded');
  } else if(await submenu.count()) {
   submenuExpanded='inline-mobile-list';
  }
  await page.keyboard.press('Escape');
  if(await page.locator('.is-menu-open').count()) await page.keyboard.press('Escape');
  results.push({base,keyboard:{skip,target,nextFocusInMain,opened,submenuExpanded,closed:await page.locator('.is-menu-open').count()===0,focusReturned:await menu.evaluate(el=>el===document.activeElement)}});
 }
 console.log(JSON.stringify(results,null,2));
 await browser.close();
 if(results.some(r=>r.violations?.length || (r.keyboard && (!r.keyboard.nextFocusInMain || !r.keyboard.opened || !r.keyboard.closed || !r.keyboard.focusReturned)))) process.exitCode=1;
})().catch(e=>{console.error(e);process.exit(1)});
