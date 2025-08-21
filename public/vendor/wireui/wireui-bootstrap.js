/* WireUI bootstrap shim
 * Replaces the inline bootstrap that <wireui:scripts /> used to inject.
 * Provides a minimal window.Wireui with hook system so the static dist/wireui.js
 * (which calls window.Wireui.dispatchHook('load')) does not fail if the dynamic
 * endpoint is disabled.
 */
(function(){
    if (typeof window === 'undefined') return;
    if (!window.Wireui) {
        const listeners = Object.create(null);
        window.Wireui = {
            on: function (hook, callback) {
                if (typeof callback !== 'function') return;
                (listeners[hook] || (listeners[hook] = [])).push(callback);
            },
            dispatchHook: function (hook) {
                const cbs = listeners[hook];
                if (!cbs) return;
                cbs.slice().forEach(cb => {
                    try { cb(); } catch (e) { console.error('WireUI hook error', hook, e); }
                });
            }
        };
    }
})();
