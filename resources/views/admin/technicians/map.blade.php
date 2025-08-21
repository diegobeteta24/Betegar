<x-admin-layout title="Mapa de Técnicos">
    <div class="py-4">
        <h2 class="text-xl font-semibold mb-4">Últimos check-ins de técnicos</h2>
    <div id="map" class="w-full rounded border bg-gray-50 relative z-0 sm:ml-0"
         style="height:clamp(300px,60vh,720px);min-height:300px;">
        <div class="absolute inset-0" id="mapInner"></div>
    </div>
    <p id="mapStatus" class="text-sm text-gray-600 mt-2"></p>
    </div>
    {{-- Leaflet assets --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (function(){
            const GUATE = [14.6349, -90.5069];
            const statusEl = () => document.getElementById('mapStatus');
            function setStatus(msg){ if(statusEl()) statusEl().textContent = msg; }
            function debounce(fn, wait=120){ let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), wait); }; }
            function adjustHeight(){
                const outer = document.getElementById('map');
                if(!outer) return;
                // En móviles ocupar más alto si viewport es bajo
                if(window.innerWidth < 640){
                    outer.style.height = 'clamp(260px,55vh,600px)';
                } else if(window.innerWidth < 1024){
                    outer.style.height = 'clamp(300px,50vh,640px)';
                } else {
                    outer.style.height = 'clamp(360px,60vh,720px)';
                }
            }

            function makeCircle(lat, lng, color, tooltip){
                return L.circleMarker([lat, lng], { radius: 7, weight: 2, color, fillColor: color, fillOpacity: .7 })
                    .bindTooltip(tooltip, { direction: 'top' });
            }

            function addLegend(map){
                const legend = L.control({position: 'bottomleft'});
                legend.onAdd = function(){
                    const div = L.DomUtil.create('div','bg-white/90 rounded shadow p-2 text-sm');
                    div.innerHTML = `
                      <div><span style="display:inline-block;width:10px;height:10px;background:#16a34a;border-radius:9999px;margin-right:6px;border:2px solid #16a34a"></span>Inicio</div>
                      <div><span style="display:inline-block;width:10px;height:10px;background:#2563eb;border-radius:9999px;margin-right:6px;border:2px solid #2563eb"></span>Última</div>
                      <div><span style="display:inline-block;width:10px;height:10px;background:#dc2626;border-radius:9999px;margin-right:6px;border:2px solid #dc2626"></span>Salida</div>
                      <div><span style="display:inline-block;width:18px;height:2px;background:#a855f7;margin-right:6px;vertical-align:middle;display:inline-block"></span>Ruta</div>
                    `;
                    return div;
                };
                legend.addTo(map);
            }

            async function fetchSessionLocations(sessionId){
                try{
                    const res = await fetch(`/api/admin/technician-sessions/${sessionId}/locations`, { credentials: 'same-origin' });
                    if(!res.ok) throw new Error('HTTP '+res.status);
                    const rows = await res.json();
                    return rows
                        .map(r => [Number(r.latitude), Number(r.longitude)])
                        .filter(([lat,lng]) => isFinite(lat) && isFinite(lng));
                }catch(e){
                    console.warn('No se pudieron obtener ubicaciones de la sesión', sessionId, e);
                    return [];
                }
            }

            async function initMap(){
                adjustHeight();
                if (!window.L) { setStatus('No se pudo cargar Leaflet'); console.warn('Leaflet no está disponible'); return; }
                const map = L.map('mapInner', { zoomControl: true }).setView(GUATE, 8);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
                addLegend(map);
                setStatus('Cargando datos...');

                try {
                    const res = await fetch('/api/admin/technicians/checkins', { credentials: 'same-origin' });
                    if (!res.ok) throw new Error('HTTP '+res.status);
                    const data = await res.json();
                    console.log('Checkins data:', data);

                    const boundsPts = [];
                    let techCount = 0, sessCount = 0;
                    data.forEach(t => {
                        techCount++;
                        (t.sessions || []).forEach(async (sess) => {
                            sessCount++;
                            const label = `<b>${t.name}</b><br>${t.email}<br>`+
                                `Inicio: ${sess.started_at || ''}`+
                                (sess.last?.logged_at ? `<br>Última: ${sess.last.logged_at}` : '')+
                                (sess.ended_at ? `<br>Salida: ${sess.ended_at}` : '');

                            if (sess.start && isFinite(sess.start.lat) && isFinite(sess.start.lng)){
                                const c = makeCircle(sess.start.lat, sess.start.lng, '#16a34a', 'Inicio');
                                c.addTo(map).bindPopup(label);
                                boundsPts.push(c.getLatLng());
                            }
                            if (sess.last && isFinite(sess.last.lat) && isFinite(sess.last.lng)){
                                const c = makeCircle(sess.last.lat, sess.last.lng, '#2563eb', 'Última');
                                c.addTo(map).bindPopup(label);
                                boundsPts.push(c.getLatLng());
                            }
                            if (sess.end && isFinite(sess.end.lat) && isFinite(sess.end.lng)){
                                const c = makeCircle(sess.end.lat, sess.end.lng, '#dc2626', 'Salida');
                                c.addTo(map).bindPopup(label);
                                boundsPts.push(c.getLatLng());
                            }

                            // Ruta de la sesión (polilínea)
                            const points = await fetchSessionLocations(sess.id);
                            let path = points;
                            // Si no hay suficientes puntos, intentar con start->last o start->end
                            if (!path || path.length < 2) {
                                const startOk = sess.start && isFinite(sess.start.lat) && isFinite(sess.start.lng);
                                const lastOk = sess.last && isFinite(sess.last.lat) && isFinite(sess.last.lng);
                                const endOk = sess.end && isFinite(sess.end.lat) && isFinite(sess.end.lng);
                                if (startOk && lastOk) path = [[sess.start.lat, sess.start.lng],[sess.last.lat, sess.last.lng]];
                                else if (startOk && endOk) path = [[sess.start.lat, sess.start.lng],[sess.end.lat, sess.end.lng]];
                            }
                            if (path && path.length >= 2){
                                const poly = L.polyline(path, { color: '#a855f7', weight: 3, opacity: 0.8 });
                                poly.addTo(map);
                            }
                        });
                    });

                    if (boundsPts.length > 0){
                        const bounds = L.latLngBounds(boundsPts);
                        map.fitBounds(bounds.pad(0.2));
                        setStatus(`Mostrando ${sessCount} sesiones de ${techCount} técnicos`);
                    } else {
                        setStatus('Sin check-ins recientes para mostrar.');
                    }

                    // Fix rendering when inside iframe or hidden container
                    setTimeout(() => { adjustHeight(); map.invalidateSize(); }, 150);
                } catch (e){
                    console.error('Mapa técnicos error:', e);
                    setStatus('No se pudieron cargar los datos del mapa.');
                }
                // Observer para cambios de tamaño del contenedor (sidebar toggle, viewport resize)
                const resizeObs = new ResizeObserver(debounce(()=> map.invalidateSize(), 80));
                resizeObs.observe(document.getElementById('map'));
                window.addEventListener('resize', debounce(()=>{ adjustHeight(); map.invalidateSize(); }, 150));
            }

            // Ensure proper size after all styles load
            window.addEventListener('load', initMap);
        })();
    </script>
</x-admin-layout>
