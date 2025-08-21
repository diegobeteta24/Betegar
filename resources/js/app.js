// resources/js/app.js
// Bootstrap básico (axios, etc.) y librerías de UI que NO deben arrancar Alpine manualmente.
import './bootstrap';
import 'flowbite'; // UI helpers (no inicia Alpine)

// IMPORTANTE:
// Eliminamos la importación y arranque manual de Alpine aquí porque:
// 1. Livewire v3 incluye Alpine internamente y dispara el evento 'alpine:init' al iniciar.
// 2. Rappasoft Livewire Tables y WireUI se registran escuchando 'alpine:init'.
// 3. Si arrancamos Alpine antes de que esas librerías adjunten sus listeners, sus componentes (p.ej. laravellivewiretable) no se registran y aparecen errores "is not defined".
// Con este cambio, diferimos el inicio de Alpine hasta que Livewire.start() se ejecute en el layout (después de cargar scripts de tablas y wireui), garantizando que todos los listeners estén presentes antes de 'alpine:init'.

console.debug('[Init] app.js cargado (sin iniciar Alpine explícitamente)');

// CSRF helpers for Sanctum stateful API
function getCookie(name){
	return document.cookie.split('; ').find(row=>row.startsWith(name+'='))?.split('=')[1] || null;
}
let __csrfPromise=null;
async function ensureCsrfCookie(force=false){
	if(!force && getCookie('XSRF-TOKEN')) return;
	if(__csrfPromise && !force) return __csrfPromise;
	__csrfPromise = fetch('/sanctum/csrf-cookie', {credentials:'include'}).catch(()=>{});
	try { await __csrfPromise; } catch(_e){}
	const after = getCookie('XSRF-TOKEN');
	console.debug('[Push][csrf] cookie fetched, value length=', after ? after.length : 0, 'force=', force);
}

// Web Push subscription helper
async function initPush(){
	if(!('serviceWorker' in navigator) || !('PushManager' in window)) return;
	try {
		// Ensure Sanctum CSRF cookie present before POST (stateful api)
		await ensureCsrfCookie();
		const reg = await navigator.serviceWorker.ready;
		let sub = await reg.pushManager.getSubscription();
		if(!sub){
			const vapid = document.querySelector('meta[name="vapid-public-key"]')?.content || window.VAPID_PUBLIC_KEY;
			if(!vapid) return;
			const convertedKey = urlBase64ToUint8Array(vapid);
			sub = await reg.pushManager.subscribe({userVisibleOnly:true, applicationServerKey: convertedKey});
		}
		// Send to backend if logged (expects sanctum cookie)
		const csrfMeta = document.querySelector('meta[name="csrf-token"]')?.content;
		const xsrf = getCookie('XSRF-TOKEN');
		console.debug('[Push][dbg] initial save attempt, xsrf length=', xsrf?xsrf.length:0);
		let res = await fetch('/api/push/subscribe', {
			method:'POST',
			headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': csrfMeta, 'X-XSRF-TOKEN': xsrf},
			body: JSON.stringify(sub),
			credentials:'include'
		});
		if(res.status === 419){
			// Retry once forcing cookie refresh via GET to /sanctum/csrf-cookie if available
			await ensureCsrfCookie(true);
			const xsrf2 = getCookie('XSRF-TOKEN');
			console.warn('[Push][dbg] first save 419, retrying with xsrf length=', xsrf2?xsrf2.length:0);
			res = await fetch('/api/push/subscribe', {
				method:'POST',
				headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content, 'X-XSRF-TOKEN': xsrf2},
				body: JSON.stringify(sub),
				credentials:'include'
			});
		}
	console.log('[Push] save status', res.status);
	if(res.ok) console.log('[Push] Subscription active'); else console.warn('[Push] subscription save failed');
	} catch(e){ console.warn('[Push] init error', e); }
}

function urlBase64ToUint8Array(base64String){
  const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
  const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
  const rawData = window.atob(base64);
  return Uint8Array.from([...rawData].map(c=>c.charCodeAt(0)));
}

// Wait for SW registration (existing sw.js) then request notification permission lazily
if('Notification' in window){
	if(Notification.permission === 'granted') initPush();
	else if(Notification.permission === 'default'){
		// Ask after a short delay to avoid blocking initial load
		setTimeout(async ()=>{
			try { const res = await Notification.requestPermission(); if(res==='granted') initPush(); } catch(_e){}
		}, 4000);
	}
}
