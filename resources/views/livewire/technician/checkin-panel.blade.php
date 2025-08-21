<div class="p-4 rounded-lg border bg-white">
    @if(!$hasCheckedInToday)
        <div class="text-center space-y-3">
            <h3 class="text-lg font-semibold">Check-in pendiente</h3>
            <p class="text-gray-600">Debes hacer check-in antes de las {{ \Carbon\Carbon::createFromFormat('H:i:s', config('app.checkin_cutoff','09:10:00'), config('app.tz_guatemala','America/Guatemala'))->isoFormat('h:mm a') }} (hora Guatemala).</p>
            <button x-data x-on:click="navigator.geolocation.getCurrentPosition(async(pos)=>{const r=await fetch('/api/technician/checkin',{method:'POST',credentials:'same-origin',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'},body:new URLSearchParams({latitude:pos.coords.latitude,longitude:pos.coords.longitude})}); if(r.ok){window.location.reload()} else {let msg='No se pudo hacer check-in'; try{const j=await r.json(); if(j && j.message){msg=j.message}}catch(e){} alert(msg)}})" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Hacer check-in</button>
        </div>
    @elseif($hasOpenSession)
        <div class="text-center space-y-3">
            <h3 class="text-lg font-semibold">Sesión abierta</h3>
            <p class="text-gray-600">Seguiremos reportando tu ubicación cuando uses la app.</p>
            <div class="flex items-center justify-center gap-3">
                <button x-data x-on:click="navigator.geolocation.getCurrentPosition(async(pos)=>{const r=await fetch('/api/technician/ping',{method:'POST',credentials:'same-origin',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'},body:new URLSearchParams({latitude:pos.coords.latitude,longitude:pos.coords.longitude})}); if(!r.ok){let msg='No se pudo enviar la ubicación'; try{const j=await r.json(); if(j && j.message){msg=j.message}}catch(e){} alert(msg)}})" class="px-3 py-2 bg-sky-600 text-white rounded-lg">Enviar ubicación</button>
                <button x-data x-on:click="navigator.geolocation.getCurrentPosition(async(pos)=>{const r=await fetch('/api/technician/checkout',{method:'POST',credentials:'same-origin',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'},body:new URLSearchParams({latitude:pos.coords.latitude,longitude:pos.coords.longitude})}); if(r.ok){window.location.reload()} else {let msg='No se pudo hacer checkout'; try{const j=await r.json(); if(j && j.message){msg=j.message}}catch(e){} alert(msg)}})" class="px-3 py-2 bg-rose-600 text-white rounded-lg">Checkout</button>
            </div>
        </div>
    @else
        <div class="text-center">
            <h3 class="text-lg font-semibold">Check-in hecho</h3>
            <p class="text-gray-600">Ya puedes ver tus órdenes pendientes.</p>
        </div>
    @endif
</div>
