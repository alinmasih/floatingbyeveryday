/* Booking flow JS — Floating */
(function () {
  'use strict';
  const app = document.getElementById('fb-booking-app');
  if (!app) return;

  const state = {
    step: 1,
    date: '',
    time: '',
    duration: '1.5 hours',
    pieces: {},
    addons: [],
    guests: 1,
    contact: { name: '', email: '', phone: '' },
    occasion: '',
    notes: '',
    coupon: '',
    quote: null,
  };

  let viewYear, viewMonth;

  /* Step navigation */
  function go(step) {
    state.step = step;
    app.querySelectorAll('.booking-pane').forEach(p => p.classList.toggle('active', +p.dataset.pane === step));
    app.querySelectorAll('.booking-step').forEach(s => {
      const n = +s.dataset.step;
      s.classList.toggle('active', n === step);
      s.classList.toggle('done', n < step);
    });
    app.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
  app.querySelectorAll('[data-back]').forEach(b => b.addEventListener('click', () => go(state.step - 1)));

  /* ----- Step 1: Calendar ----- */
  async function loadAvailability(y, m) {
    const r = await fetch(`${FBData.restUrl}/availability?year=${y}&month=${m}`);
    return r.json();
  }
  function renderCalendar() {
    const title = document.getElementById('fb-cal-title');
    const grid = document.getElementById('fb-cal-grid');
    const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    title.textContent = `${months[viewMonth-1]} ${viewYear}`;
    grid.innerHTML = '';
    const firstDow = new Date(viewYear, viewMonth-1, 1).getDay();
    const daysInMonth = new Date(viewYear, viewMonth, 0).getDate();
    for (let i = 0; i < firstDow; i++) {
      const e = document.createElement('div'); e.className = 'calendar-cell empty'; grid.appendChild(e);
    }
    loadAvailability(viewYear, viewMonth).then(avail => {
      for (let d = 1; d <= daysInMonth; d++) {
        const date = `${viewYear}-${String(viewMonth).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        const cell = document.createElement('div');
        cell.className = 'calendar-cell';
        cell.textContent = d;
        const a = avail[date];
        if (!a || !a.available) {
          cell.classList.add('disabled');
          if (a && a.reason === 'closed') cell.title = 'Closed';
        } else {
          cell.classList.add('has-slots');
          cell.dataset.date = date;
          cell.addEventListener('click', () => selectDate(date));
        }
        grid.appendChild(cell);
      }
    });
  }
  async function selectDate(date) {
    state.date = date;
    state.time = '';
    document.querySelectorAll('#fb-cal-grid .calendar-cell').forEach(c => c.classList.toggle('selected', c.dataset.date === date));
    document.getElementById('fb-slots-wrap').style.display = 'block';
    const slotGrid = document.getElementById('fb-slot-grid');
    slotGrid.innerHTML = '<p class="muted">Loading time slots...</p>';
    const r = await fetch(`${FBData.restUrl}/slots?date=${date}`);
    const data = await r.json();
    slotGrid.innerHTML = '';
    data.slots.forEach(s => {
      const el = document.createElement('div');
      el.className = 'slot' + (s.full ? ' full' : '');
      el.innerHTML = `<div class="slot__time">${s.label}</div><div class="slot__seats">${s.full ? 'Full' : s.remaining + ' seats left'}</div>`;
      if (!s.full) el.addEventListener('click', () => selectSlot(s.time, el));
      slotGrid.appendChild(el);
    });
    document.getElementById('fb-next-1').disabled = true;
  }
  function selectSlot(time, el) {
    state.time = time;
    document.querySelectorAll('#fb-slot-grid .slot').forEach(s => s.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('fb-next-1').disabled = false;
  }
  document.querySelectorAll('input[name="fb-duration"]').forEach(r => {
    r.addEventListener('change', e => {
      state.duration = e.target.value;
      document.querySelectorAll('#fb-duration .slot').forEach(s => s.classList.toggle('selected', s.querySelector('input').checked));
    });
  });
  document.querySelectorAll('#fb-duration .slot').forEach(s => {
    if (s.querySelector('input').checked) s.classList.add('selected');
    s.addEventListener('click', () => { s.querySelector('input').checked = true; s.querySelector('input').dispatchEvent(new Event('change')); });
  });

  document.getElementById('fb-cal-prev').addEventListener('click', () => {
    viewMonth--;
    if (viewMonth < 1) { viewMonth = 12; viewYear--; }
    renderCalendar();
  });
  document.getElementById('fb-cal-next').addEventListener('click', () => {
    viewMonth++;
    if (viewMonth > 12) { viewMonth = 1; viewYear++; }
    renderCalendar();
  });
  document.getElementById('fb-next-1').addEventListener('click', () => { if (state.date && state.time) { loadPieces(); go(2); } });

  /* ----- Step 2: Pieces ----- */
  let allPieces = [];
  async function loadPieces() {
    if (allPieces.length) return;
    const r = await fetch(`${FBData.restUrl}/pieces`);
    allPieces = await r.json();
    renderPieces('all');
    const cats = ['all', ...new Set(allPieces.map(p => p.category).filter(Boolean))];
    const filters = document.getElementById('fb-piece-filters');
    filters.innerHTML = cats.map((c,i) => `<button class="filter-chip${i===0?' active':''}" data-fil="${c}">${c==='all'?'All pieces':c}</button>`).join('');
    filters.querySelectorAll('.filter-chip').forEach(b => b.addEventListener('click', e => {
      filters.querySelectorAll('.filter-chip').forEach(x => x.classList.remove('active'));
      b.classList.add('active');
      renderPieces(b.dataset.fil);
    }));
  }
  function renderPieces(filter) {
    const grid = document.getElementById('fb-piece-grid');
    const filtered = filter === 'all' ? allPieces : allPieces.filter(p => p.category === filter);
    grid.innerHTML = filtered.map(p => `
      <div class="piece-card" data-id="${p.id}">
        ${state.pieces[p.id] ? `<div class="piece-card__qty">${state.pieces[p.id].qty}</div>` : ''}
        <div class="piece-card__image">${p.image ? `<img src="${p.image}" alt="${p.name}" loading="lazy">` : ''}</div>
        <div class="piece-card__body">
          <div class="piece-card__cat">${p.category || ''}</div>
          <h3 class="piece-card__name">${p.name}</h3>
          <div class="piece-card__row">
            <div class="piece-card__price">₹${p.price}</div>
            <span class="piece-card__diff ${p.difficulty}">${p.difficulty}</span>
          </div>
          <div class="qty-controls" style="margin-top:12px" onclick="event.stopPropagation()">
            <button type="button" data-act="dec" data-id="${p.id}">−</button>
            <span data-qty="${p.id}">${state.pieces[p.id]?.qty || 0}</span>
            <button type="button" data-act="inc" data-id="${p.id}">+</button>
          </div>
        </div>
      </div>
    `).join('');
    grid.querySelectorAll('.piece-card').forEach(card => {
      const id = +card.dataset.id;
      card.classList.toggle('selected', !!state.pieces[id]);
      card.addEventListener('click', e => {
        if (e.target.closest('.qty-controls')) return;
        bumpPiece(id, 1);
      });
    });
    grid.querySelectorAll('.qty-controls button').forEach(b => {
      b.addEventListener('click', () => bumpPiece(+b.dataset.id, b.dataset.act === 'inc' ? 1 : -1));
    });
  }
  function bumpPiece(id, delta) {
    const piece = allPieces.find(p => p.id === id);
    if (!piece) return;
    const cur = state.pieces[id]?.qty || 0;
    const next = Math.max(0, cur + delta);
    if (next === 0) delete state.pieces[id];
    else state.pieces[id] = { id, qty: next, name: piece.name, price: piece.price };
    renderPieces(document.querySelector('#fb-piece-filters .filter-chip.active')?.dataset.fil || 'all');
  }
  document.getElementById('fb-next-2').addEventListener('click', () => {
    if (Object.keys(state.pieces).length === 0) {
      alert('Please pick at least one pottery piece, or go back to Step 1.');
      return;
    }
    go(3);
  });

  /* ----- Step 3: Addons ----- */
  document.getElementById('fb-next-3').addEventListener('click', () => {
    state.addons = Array.from(document.querySelectorAll('input[name="addon"]:checked')).map(c => c.value);
    go(4);
  });

  /* ----- Step 4: Guests ----- */
  document.getElementById('fb-next-4').addEventListener('click', () => {
    const g = +document.getElementById('fb-guests').value || 1;
    const name = document.getElementById('fb-name').value.trim();
    const email = document.getElementById('fb-email').value.trim();
    const phone = document.getElementById('fb-phone').value.trim();
    if (g < 1 || !name || !email || !phone) { alert('Please fill in your name, email and phone.'); return; }
    if (!/^\S+@\S+\.\S+$/.test(email)) { alert('Please enter a valid email.'); return; }
    state.guests = g;
    state.contact = { name, email, phone };
    state.occasion = document.getElementById('fb-occasion').value;
    state.notes = document.getElementById('fb-notes').value;
    refreshQuote();
    go(5);
  });

  /* ----- Step 5: Review ----- */
  async function refreshQuote() {
    const body = {
      guests: state.guests,
      pieces: Object.values(state.pieces).map(p => ({ id: p.id, qty: p.qty })),
      addons: state.addons,
      coupon: state.coupon,
    };
    const r = await fetch(`${FBData.restUrl}/quote`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body),
    });
    state.quote = await r.json();
    renderSummary();
  }
  function renderSummary() {
    const q = state.quote;
    const wrap = document.getElementById('fb-summary');
    if (!q) { wrap.innerHTML = '<p>Loading...</p>'; return; }
    let pieces = q.pieces.map(p => `<div class="summary-row"><span>${p.name} × ${p.qty}</span><span>₹${p.total.toLocaleString('en-IN')}</span></div>`).join('');
    let addons = q.addons.map(a => `<div class="summary-row"><span>${a.name}</span><span>₹${a.amount.toLocaleString('en-IN')}</span></div>`).join('');
    const dateStr = new Date(state.date).toLocaleDateString('en-IN', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
    const timeStr = new Date(`2000-01-01T${state.time}`).toLocaleTimeString('en-IN', { hour:'numeric', minute:'2-digit' });

    wrap.innerHTML = `
      <div class="summary">
        <h4>Your visit</h4>
        <div class="summary-row"><span>${dateStr}</span><span>${timeStr}</span></div>
        <div class="summary-row"><span>Duration</span><span>${state.duration}</span></div>
        <div class="summary-row"><span>Guests</span><span>${q.guests}</span></div>
      </div>
      <div class="summary" style="margin-top:14px">
        <h4>Pieces</h4>
        ${pieces}
        <div class="summary-row"><span>Studio fee × ${q.guests}</span><span>₹${q.studio_total.toLocaleString('en-IN')}</span></div>
        ${addons ? `<div style="border-top:1px solid var(--c-line); margin:10px 0; padding-top:10px"><h4 style="font-size:14px; letter-spacing:0.06em; text-transform:uppercase; color:var(--c-ink-mute); margin:0 0 8px">Add-ons</h4>${addons}</div>` : ''}
        <div class="summary-row" style="border-top:1px solid var(--c-line); margin-top:10px; padding-top:14px"><span>Subtotal</span><span>₹${q.subtotal.toLocaleString('en-IN')}</span></div>
        ${q.group_discount ? `<div class="summary-row" style="color:var(--c-clay-deep)"><span>Group discount (${q.group_discount_pct}%)</span><span>−₹${q.group_discount.toLocaleString('en-IN')}</span></div>` : ''}
        ${q.coupon_discount ? `<div class="summary-row" style="color:var(--c-clay-deep)"><span>Coupon ${q.coupon_code}</span><span>−₹${q.coupon_discount.toLocaleString('en-IN')}</span></div>` : ''}
        <div class="summary-row"><span>GST (${q.gst_rate}%)</span><span>₹${q.gst.toLocaleString('en-IN')}</span></div>
        <div class="summary-row summary-row--total"><span>Grand total</span><span>₹${q.total.toLocaleString('en-IN')}</span></div>
      </div>
    `;
  }
  document.getElementById('fb-apply-coupon').addEventListener('click', async () => {
    const code = document.getElementById('fb-coupon').value.trim();
    state.coupon = code;
    const msg = document.getElementById('fb-coupon-msg');
    msg.textContent = 'Checking...';
    await refreshQuote();
    if (state.quote.coupon_code) msg.textContent = `Coupon applied: -₹${state.quote.coupon_discount}`;
    else msg.textContent = 'That code is not valid right now.';
  });
  document.getElementById('fb-confirm').addEventListener('click', async () => {
    if (!document.getElementById('fb-terms').checked) { alert('Please accept the studio rules.'); return; }
    const btn = document.getElementById('fb-confirm');
    btn.disabled = true;
    btn.textContent = 'Confirming...';
    try {
      const r = await fetch(`${FBData.restUrl}/book`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          date: state.date,
          time: state.time,
          duration: state.duration,
          guests: state.guests,
          pieces: Object.values(state.pieces).map(p => ({ id: p.id, qty: p.qty })),
          addons: state.addons,
          contact: state.contact,
          occasion: state.occasion,
          notes: state.notes,
          coupon: state.coupon,
          terms: true,
        }),
      });
      const j = await r.json();
      if (j.ok) {
        document.getElementById('fb-confirm-num').textContent = j.confirm;
        const q = j.quote;
        const dateStr = new Date(state.date).toLocaleDateString('en-IN', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
        const timeStr = new Date(`2000-01-01T${state.time}`).toLocaleTimeString('en-IN', { hour:'numeric', minute:'2-digit' });
        document.getElementById('fb-confirm-summary').innerHTML = `
          <div class="summary">
            <div class="summary-row"><span>${dateStr}</span><span>${timeStr}</span></div>
            <div class="summary-row"><span>Guests</span><span>${q.guests}</span></div>
            <div class="summary-row summary-row--total"><span>Total</span><span>₹${q.total.toLocaleString('en-IN')}</span></div>
          </div>
        `;
        go(6);
      } else {
        alert(j.message || 'Something went wrong. Please try again.');
        btn.disabled = false; btn.textContent = 'Confirm and pay';
      }
    } catch (e) {
      alert('Network error. Please try again.');
      btn.disabled = false; btn.textContent = 'Confirm and pay';
    }
  });

  /* Init */
  const today = new Date();
  viewYear = today.getFullYear(); viewMonth = today.getMonth() + 1;
  renderCalendar();

})();
