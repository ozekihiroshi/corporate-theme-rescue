const { chromium } = require(process.env.PLAYWRIGHT_MODULE);
const fs = require('fs');
const path = require('path');
const assert = (ok, message) => { if (!ok) throw new Error(message); };
const base = process.env.OC_REVIEW_URL || 'http://localhost:8090/review-fixture.php';
const output = process.env.OC_AUDIT_OUTPUT;

(async () => {
  const browser = await chromium.launch({ executablePath: process.env.BROWSER_EXE });
  const results = [];
  try {
    for (const menu of ['auto', 'custom']) {
      for (const width of [1440, 390, 320]) {
        const page = await browser.newPage({ viewport: { width, height: 900 } });
        await page.goto(`${base}?menu=${menu}`, { waitUntil: 'networkidle' });
        const nav = page.locator('nav').first();
        if (width < 600) {
          const open = nav.locator('.wp-block-navigation__responsive-container-open');
          // Reach the opener from the document by Tab, then activate with Enter.
          for (let n=0;n<80;n++) {
            await page.keyboard.press('Tab', {delay:100});
            if (await open.evaluate(el => el===document.activeElement)) break;
          }
          assert(await open.evaluate(el => el===document.activeElement), 'Menu opener unreachable');
          await page.keyboard.press('Enter', {delay:100});
          await nav.locator('.is-menu-open').waitFor();
        }
        const parent=nav.getByRole('link',{name:'Review parent',exact:true});
        const child=nav.getByRole('link',{name:'Review child',exact:true});
        const grandchild=nav.getByRole('link',{name:'Review grandchild',exact:true});
        if (width >=600) {
          await parent.hover();
          await child.waitFor({state:'visible'});
          await child.hover();
          await grandchild.waitFor({state:'visible'});
          await page.mouse.move(0,0);
          await page.reload({waitUntil:'networkidle'});
        }
        const visited = new Set();
        const initialFocus = await page.evaluate(()=>document.activeElement?.textContent?.trim());
        if (['Review parent','Review child','Review grandchild'].includes(initialFocus)) visited.add(initialFocus);
        let reached=false;
        for (let n=0;n<150;n++) {
          await page.keyboard.press('Tab', {delay:100});
          const active=await page.evaluate(()=>({text:document.activeElement?.textContent?.trim(),expanded:document.activeElement?.getAttribute('aria-expanded'),tag:document.activeElement?.tagName}));
          if (active.tag==='BUTTON' && active.expanded==='false') {
            await page.keyboard.press('Enter', {delay:100});
            await page.waitForFunction(()=>document.activeElement?.getAttribute('aria-expanded')==='true');
          }
          if (['Review parent','Review child','Review grandchild'].includes(active.text)) visited.add(active.text);
          if (active.text==='Review grandchild') { reached=true; break; }
        }
        assert(reached && visited.size===3, `${menu}/${width}: hierarchy not keyboard reachable: ${[...visited]}`);
        await page.keyboard.press('Shift+Tab', {delay:100});
        await page.keyboard.press('Tab', {delay:100});
        assert(await grandchild.evaluate(el=>el===document.activeElement), 'Reverse traversal failed');
        if (width>=600) {
          await page.keyboard.press('Escape', {delay:100});
          await grandchild.waitFor({state:'hidden'});
          assert(await page.evaluate(()=>document.activeElement?.matches('button.wp-block-navigation-submenu__toggle')),'Desktop Escape focus lost');
          await page.waitForFunction(()=>document.activeElement?.getAttribute('aria-expanded')==='false');
          await page.keyboard.press('Space', {delay:100});
          await page.waitForFunction(()=>document.activeElement?.getAttribute('aria-expanded')==='true');
          let reopened=false;
          const reopenTrace=[];
          for(let n=0;n<10;n++) {
            await page.keyboard.press('Tab', {delay:100});
            const focus=await page.evaluate(()=>({text:document.activeElement?.textContent?.trim(),expanded:document.activeElement?.getAttribute('aria-expanded')}));
            reopenTrace.push({...focus,text:focus.text?.slice(0,80)});
            if(focus.text==='Review grandchild'){reopened=true;break;}
            if(focus.expanded==='false') {
              await page.keyboard.press('Space', {delay:100});
              await page.waitForFunction(()=>document.activeElement?.getAttribute('aria-expanded')==='true');
            }
          }
          assert(reopened,'Space did not reopen submenu hierarchy: '+JSON.stringify(reopenTrace));
        }
        assert(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth+1),'Open menu causes horizontal overflow');
        if(output){fs.mkdirSync(output,{recursive:true});await page.screenshot({path:path.join(output,`${menu}-${width}-menu.png`),fullPage:width>=600});}
        if(width<600){
          await page.keyboard.press('Escape', {delay:100});
          assert(await nav.locator('.is-menu-open').count()===0,'Mobile menu Escape failed');
          assert(await nav.locator('.wp-block-navigation__responsive-container-open').evaluate(el=>el===document.activeElement),'Focus not restored');
          await nav.locator('.wp-block-navigation__responsive-container-open').click();
        }
        if(width>=600) await page.keyboard.press('Enter', {delay:100});
        else await grandchild.click();
        await page.waitForLoadState('load');
        assert((await page.locator('main').innerText()).includes('Review grandchild'),'Child destination failed');
        await page.goto(`${base}?menu=${menu}`, {waitUntil:'networkidle'});
        assert(await page.locator('#commentform').count()===1,'Page comment form missing');
        assert((await page.locator('main').innerText()).includes('Existing review comment parent'),'Existing page comment missing');
        const reply=page.locator('.comment-reply-link').first();
        await reply.click();
        assert(await page.locator('#comment_parent').inputValue()!=='0','Page reply not activated');
        await page.locator('#cancel-comment-reply-link').click();
        assert(await page.locator('#comment_parent').inputValue()==='0','Page reply cancel failed');
        const overflow=await page.evaluate(()=>document.documentElement.scrollWidth>innerWidth+1);
        assert(!overflow, `${width}: horizontal overflow`);
        if(output){fs.mkdirSync(output,{recursive:true});await page.screenshot({path:path.join(output,`${menu}-${width}.png`),fullPage:true});}
        results.push({menu,width,hover:width>=600,keyboard:true,mobileEscape:width<600,link:true,comments:true,reply:true});
        await page.close();
      }
    }
    const page=await browser.newPage();
    for (const mode of ['closed','protected']) {
      await page.goto(`${base}?case=${mode}`,{waitUntil:'networkidle'});
      assert(await page.locator('#commentform').count()===0,`${mode}: form exposed`);
      assert((await page.locator('main').innerText()).includes('Existing review comment')===(mode==='closed'),`${mode}: comments visibility`);
    }
    console.log(JSON.stringify({passed:true,results},null,2));
    if(output)fs.writeFileSync(path.join(output,'results.json'),JSON.stringify({passed:true,results},null,2));
  } finally { await browser.close(); }
})().catch(e=>{console.error(e);process.exitCode=1;});
