<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" type="image/png" href="images/logo.png">
<script src="IN/js/support.js"></script>
</head>
<body>
<x-dc>
<helmet>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Playfair+Display:ital,wght@0,500;1,500&display=swap" rel="stylesheet">
<style>
  body { margin: 0; }
  @keyframes sparklePop { 0% { transform: scale(0) rotate(0deg); opacity: 0; } 40% { opacity: 1; } 100% { transform: scale(1) rotate(90deg) translateY(-24px); opacity: 0; } }
  @keyframes wobble { 0%,100% { transform: rotate(-1deg); } 50% { transform: rotate(1deg); } }
  @keyframes twinkle { 0%,100% { opacity: 0; transform: scale(0.4); } 50% { opacity: 1; transform: scale(1); } }
  @keyframes floaty { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
  @keyframes shimmer { 0% { transform: translateX(-120%); } 100% { transform: translateX(120%); } }
  .mischief-star::before { content: '\2726'; }
  .om-sparkle { position: fixed; pointer-events: none; z-index: 9999; font-size: 14px; animation: sparklePop 0.9s ease-out forwards; }
</style>
</helmet>

<div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height: 100vh; display: flex; flex-direction: column; background: #150e22;">

  <header style="position: fixed; top: 0; left: 0; right: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; height: 78px; padding: 0 32px; background: rgba(21, 14, 34, 0.55); backdrop-filter: blur(14px); border-bottom: 2px solid rgba(255,209,102,0.25);">
    <a href="index.php" style="display: flex; align-items: center; gap: 12px; text-decoration: none;">
      <img src="images/logo3.png" alt="The Obscured Index logo" style="height: 48px; width: 48px; object-fit: contain; filter: drop-shadow(0 0 8px rgba(255,209,102,0.5)); animation: wobble 4s ease-in-out infinite;">
      <span style="font-family: 'Cinzel', serif; font-weight: 700; font-size: 1.2rem; color: #fff6e5; letter-spacing: 0.02em;">The Obscured Index</span>
    </a>
    <nav style="display: flex; align-items: center; gap: 26px;">
      <a href="index.php" style="font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 0.04em; color: rgba(255,246,229,0.8); text-decoration: none;" style-hover="color: #ffd166;">HOME</a>
      <a href="login.php" style="font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 0.04em; color: rgba(255,246,229,0.8); text-decoration: none;" style-hover="color: #ffd166;">LOGIN</a>
    </nav>
  </header>

  <main style="flex: 1; position: relative; display: flex; align-items: center; justify-content: center; overflow: hidden;">
      <img src="images/index-background.jpeg" alt="" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0;">
      <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(14,10,23,0.6) 0%, rgba(14,10,23,0.45) 45%, rgba(14,10,23,0.85) 100%); z-index: 1;"></div>

      <div style="position: absolute; top: 20%; left: 14%; width: 5px; height: 5px; border-radius: 50%; background: #fff; box-shadow: 0 0 8px 2px rgba(255,255,255,0.8); animation: twinkle 3.2s ease-in-out infinite; z-index: 2;"></div>
      <div style="position: absolute; top: 65%; left: 85%; width: 4px; height: 4px; border-radius: 50%; background: #fff; box-shadow: 0 0 6px 2px rgba(255,255,255,0.7); animation: twinkle 2.8s ease-in-out infinite 0.8s; z-index: 2;"></div>

      <div style="position: relative; z-index: 3; max-width: 740px; margin: 0 32px; padding: 58px 50px; text-align: center; background: rgba(20, 15, 32, 0.5); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.14); border-radius: 4px; box-shadow: 0 20px 60px rgba(0,0,0,0.4); animation: floaty 6s ease-in-out infinite;">
        <p style="font-family: 'Cinzel', serif; font-size: 0.7rem; letter-spacing: 0.3em; color: #c9bffc; text-align: center; margin: 0 0 16px;">MANHWA COLLECTION TRACKER</p>
        <h1 style="font-family: 'Playfair Display', serif; font-weight: 500; font-size: clamp(2.3rem, 5vw, 3.6rem); color: #fff6e5; margin: 0 0 18px; text-shadow: 0 2px 20px rgba(0,0,0,0.5);">The Obscured Index</h1>
        <p style="font-family: 'Playfair Display', serif; font-style: italic; font-size: 1.2rem; color: rgba(255,246,229,0.88); margin: 0 0 32px; line-height: 1.5;">Where lost scrolls of manhwa await&mdash;if you're brave (or bored) enough to enter.</p>

        <div style="display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; margin-bottom: 36px;">
          <span style="font-family: 'Cinzel', serif; font-size: 0.72rem; letter-spacing: 0.06em; color: #fff; background: rgba(162,155,254,0.3); padding: 7px 15px; border-radius: 999px; font-weight: 600; display: inline-block; white-space: nowrap;">TRACK</span>
          <span style="font-family: 'Cinzel', serif; font-size: 0.72rem; letter-spacing: 0.06em; color: #fff; background: rgba(162,155,254,0.3); padding: 7px 15px; border-radius: 999px; font-weight: 600; display: inline-block; white-space: nowrap;">CURATE</span>
          <span style="font-family: 'Cinzel', serif; font-size: 0.72rem; letter-spacing: 0.06em; color: #fff; background: rgba(162,155,254,0.3); padding: 7px 15px; border-radius: 999px; font-weight: 600; display: inline-block; white-space: nowrap;">REDISCOVER</span>
        </div>

        <div style="display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
          <a href="login.php" style="font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 0.04em; text-decoration: none; color: #0e0a17; background: linear-gradient(135deg, #a29bfe, #8a2be2); padding: 14px 28px; border-radius: 999px; font-weight: 600; box-shadow: 0 8px 24px rgba(138,43,226,0.4); white-space: nowrap;" style-hover="box-shadow: 0 10px 30px rgba(138,43,226,0.55);">ENTER THE INDEX</a>
          <a href="registration.php" style="font-family: 'Cinzel', serif; font-size: 0.85rem; letter-spacing: 0.04em; text-decoration: none; color: rgba(255,255,255,0.8); background: transparent; border: 1px solid rgba(255,255,255,0.25); padding: 14px 28px; border-radius: 999px; font-weight: 600; white-space: nowrap;" style-hover="border-color: rgba(255,255,255,0.5); color: #fff;">CREATE ACCOUNT</a>
        </div>
      </div>
  </main>

  <footer style="position: relative; z-index: 3; background: rgba(21,14,34,0.95); border-top: 2px dashed rgba(255,209,102,0.25); padding: 22px 32px; text-align: center;">
    <p style="font-family: 'Cinzel', serif; font-size: 0.75rem; letter-spacing: 0.05em; color: rgba(255,246,229,0.55); margin: 0;">&copy; <?php echo date('Y'); ?> &mdash; The Obscured Index. All rights reserved. &#10024;</p>
  </footer>
</div>

</x-dc>
<script type="text/x-dc" data-dc-script>
class Component extends DCLogic {
  componentDidMount() {
    const colors = ['#ffd166', '#ff8fd0', '#a06bff', '#ffffff'];
    const glyphs = ['\u2726', '\u2727', '\u2728', '\u2734'];
    this._onMove = (e) => {
      if (Math.random() > 0.88) {
        const el = document.createElement('div');
        el.className = 'om-sparkle';
        el.textContent = glyphs[Math.floor(Math.random() * glyphs.length)];
        el.style.left = (e.clientX - 6) + 'px';
        el.style.top = (e.clientY - 6) + 'px';
        el.style.color = colors[Math.floor(Math.random() * colors.length)];
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 900);
      }
    };
    document.addEventListener('mousemove', this._onMove);
  }
  componentWillUnmount() {
    document.removeEventListener('mousemove', this._onMove);
  }
  renderVals() { return {}; }
}

</script>
</body>
</html>
