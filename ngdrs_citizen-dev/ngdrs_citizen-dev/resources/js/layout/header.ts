export function initDateTime() {
  const el = document.getElementById('liveDateTime');
  if (!el) return;

  function update() {
    const now = new Date();

    const date = now.toLocaleDateString('en-GB', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
      weekday: 'short',
    });

    const time = now.toLocaleTimeString('en-US', {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      hour12: true,
    });

    // Example: 30 Jan 2026 | Fri | 05:12:08 PM
    const [weekday, rest] = date.split(', ');
    el!.textContent = `${rest} | ${weekday} | ${time}`;
  }

  update();
  setInterval(update, 1000);
}
