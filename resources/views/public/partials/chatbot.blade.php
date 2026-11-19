<div x-data="asistenteSetc()" class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-50 select-none" x-cloak>

    {{-- ═══════════════════════════════════════════════════════ BOTÓN FLOTANTE ══ --}}
    <button
        type="button"
        @click="toggleChat()"
        class="group flex items-center gap-2.5 px-3.5 py-2.5 sm:px-4 sm:py-3 rounded-full bg-[#001229] hover:bg-[#00224d] dark:bg-[#0051d5] dark:hover:bg-[#1d4ed8] text-white shadow-2xl shadow-blue-950/40 hover:shadow-blue-900/60 transition-all duration-300 transform hover:scale-105 active:scale-95 cursor-pointer border border-blue-400/20"
        aria-label="Abrir Asistente Virtual SETC"
    >
        {{-- Avatar con pulso animado --}}
        <div class="relative w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center shrink-0 shadow-inner">
            <span class="material-symbols-outlined text-white text-lg sm:text-xl">smart_toy</span>
            <span class="absolute -top-0.5 -right-0.5 flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500 border-2 border-[#001229]"></span>
            </span>
        </div>

        {{-- Texto en escritorio --}}
        <div class="hidden sm:flex flex-col text-left leading-tight pr-1">
            <span class="text-xs font-bold tracking-tight text-white flex items-center gap-1">
                Asistente Virtual
                <span class="text-[9px] px-1.5 py-0.2 rounded bg-blue-500/30 text-blue-200 border border-blue-400/30 font-mono">IA</span>
            </span>
            <span class="text-[10px] text-blue-200">¿Dudas con tu trámite o cita?</span>
        </div>
    </button>

    {{-- ═══════════════════════════════════════════════════════ VENTANA DE CHAT ══ --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-250 transform"
        x-transition:enter-start="opacity-0 translate-y-6 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150 transform"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-6 scale-95"
        @click.outside="open = false"
        class="fixed bottom-20 right-3 sm:right-6 w-[calc(100vw-1.5rem)] sm:w-[410px] max-w-[430px] h-[550px] max-h-[82vh] bg-white dark:bg-[#0c1a2e] rounded-2xl shadow-2xl border border-slate-200/90 dark:border-slate-700/80 flex flex-col overflow-hidden z-50 text-slate-800 dark:text-slate-100"
        role="dialog"
        aria-modal="true"
        aria-label="Ventana de chat del Asistente Virtual"
    >
        {{-- Cabecera del Chat --}}
        <div class="px-4 py-3 bg-[#001229] dark:bg-[#08121f] text-white flex items-center justify-between border-b border-slate-800 shrink-0">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center shrink-0 shadow-xs">
                    <span class="material-symbols-outlined text-white text-lg">smart_toy</span>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5">
                        <span class="font-bold text-xs truncate text-white tracking-tight">Asistente Virtual SETC</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">En línea</span>
                    </div>
                    <span class="text-[10px] text-slate-400 block truncate">Orientación de trámites, citas y recaudos</span>
                </div>
            </div>

            <div class="flex items-center gap-1 shrink-0">
                {{-- Limpiar conversación --}}
                <button
                    type="button"
                    @click="reiniciar()"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-colors"
                    title="Reiniciar conversación"
                >
                    <span class="material-symbols-outlined text-base">restart_alt</span>
                </button>
                {{-- Cerrar chat --}}
                <button
                    type="button"
                    @click="open = false"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-colors"
                    title="Cerrar ventana"
                >
                    <span class="material-symbols-outlined text-base">close</span>
                </button>
            </div>
        </div>

        {{-- Contenedor de Mensajes Scrollable --}}
        <div id="chatbot-messages-container" class="flex-1 overflow-y-auto p-4 space-y-3.5 text-xs bg-slate-50/60 dark:bg-[#070e17]/80 custom-scroll">
            <template x-for="(msg, index) in mensajes" :key="index">
                <div class="flex flex-col" :class="msg.role === 'user' ? 'items-end' : 'items-start'">
                    {{-- Etiqueta / Rol --}}
                    <div class="flex items-center gap-1 mb-1 px-1">
                        <span class="text-[10px] text-slate-400 font-medium" x-text="msg.role === 'user' ? 'Tú' : 'Asistente SETC'"></span>
                        <span class="text-[9px] text-slate-400" x-text="'• ' + msg.time"></span>
                    </div>

                    {{-- Globo de Mensaje --}}
                    <div
                        class="max-w-[85%] rounded-2xl p-3 shadow-xs leading-relaxed select-text"
                        :class="msg.role === 'user'
                            ? 'bg-[#0051d5] text-white rounded-br-xs'
                            : 'bg-white dark:bg-[#0e1c2e] border border-slate-200/90 dark:border-slate-800 text-slate-800 dark:text-slate-200 rounded-bl-xs'"
                    >
                        <div x-html="renderMarkdown(msg.content)"></div>
                    </div>
                </div>
            </template>

            {{-- Indicador de Escribiendo (Loading Dots) --}}
            <div x-show="loading" class="flex items-start gap-2 pt-1" x-cloak>
                <div class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-950 text-[#0051d5] flex items-center justify-center shrink-0 mt-0.5">
                    <span class="material-symbols-outlined text-xs">smart_toy</span>
                </div>
                <div class="bg-white dark:bg-[#0e1c2e] border border-slate-200/80 dark:border-slate-800 rounded-2xl px-3 py-2 flex items-center gap-1 shadow-2xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-bounce"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-bounce" style="animation-delay: 150ms"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-bounce" style="animation-delay: 300ms"></span>
                </div>
            </div>

            {{-- Sugerencias Rápidas --}}
            <div x-show="sugerencias.length > 0 && !loading" class="pt-2 space-y-1.5" x-cloak>
                <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block px-1">Preguntas sugeridas:</span>
                <div class="flex flex-wrap gap-1.5">
                    <template x-for="(sug, sIndex) in sugerencias" :key="sIndex">
                        <button
                            type="button"
                            @click="enviar(sug)"
                            class="text-left text-[11px] px-2.5 py-1.5 rounded-xl bg-white dark:bg-[#111f33] hover:bg-blue-50 dark:hover:bg-blue-950/60 text-[#0051d5] dark:text-blue-300 border border-blue-200/70 dark:border-blue-900/60 transition-colors shadow-2xs cursor-pointer"
                            x-text="sug"
                        ></button>
                    </template>
                </div>
            </div>
        </div>

        {{-- Formulario de Entrada --}}
        <div class="p-3 bg-white dark:bg-[#0a1626] border-t border-slate-200/90 dark:border-slate-800 shrink-0">
            <form @submit.prevent="enviar()" class="relative flex items-center gap-2">
                <input
                    type="text"
                    x-model="input"
                    :disabled="loading"
                    placeholder="Escribe tu consulta aquí..."
                    class="flex-1 py-2 pl-3.5 pr-10 rounded-xl bg-slate-50 dark:bg-[#070e17] text-slate-900 dark:text-white border border-slate-200 dark:border-slate-700 placeholder:text-slate-400 text-xs focus:outline-none focus:ring-2 focus:ring-[#0051d5]/30 focus:border-[#0051d5] transition-all disabled:opacity-60"
                    maxlength="400"
                />
                <button
                    type="submit"
                    :disabled="loading || !input.trim()"
                    class="absolute right-1.5 top-1/2 -translate-y-1/2 w-7 h-7 rounded-lg bg-[#0051d5] hover:bg-[#1d4ed8] text-white flex items-center justify-center transition-colors disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer shadow-xs"
                    aria-label="Enviar mensaje"
                >
                    <span class="material-symbols-outlined text-sm">send</span>
                </button>
            </form>
            <div class="mt-1.5 flex items-center justify-between text-[10px] text-slate-400 px-1">
                <span>Información oficial del catálogo SETC</span>
                <span class="font-mono">v1.0 IA</span>
            </div>
        </div>
    </div>

</div>

{{-- Script del componente Alpine.js para el Asistente Virtual --}}
<script>
    function asistenteSetc() {
        return {
            open: false,
            input: '',
            loading: false,
            mensajes: [
                {
                    role: 'assistant',
                    content: '👋 ¡Hola! Soy el **Asistente Virtual Oficial de SETC**.\n\nPuedo orientarte con los **requisitos obligatorios** de trámites, reglas para agendar tu **cita en ventanilla**, estados de tu expediente y formatos de carga en PDF.\n\n¿En qué te puedo apoyar hoy?',
                    time: 'Ahora'
                }
            ],
            sugerencias: [
                '¿Qué requisitos piden para Licencia de Funcionamiento?',
                '¿Cómo y cuándo puedo agendar una cita?',
                '¿Cuáles son todos los trámites disponibles?'
            ],

            toggleChat() {
                this.open = !this.open;
                if (this.open) {
                    this.scrollToBottom();
                }
            },

            reiniciar() {
                this.mensajes = [
                    {
                        role: 'assistant',
                        content: 'Conversación reiniciada. ¿Qué otra consulta sobre trámites o citas tienes?',
                        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                    }
                ];
                this.sugerencias = [
                    'Requisitos de Licencia de Funcionamiento',
                    '¿Cómo agendar mi cita?',
                    'Ver trámites disponibles'
                ];
                this.input = '';
            },

            async enviar(texto = null) {
                const query = (texto || this.input).trim();
                if (!query || this.loading) return;

                this.input = '';
                const horaActual = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                this.mensajes.push({
                    role: 'user',
                    content: query,
                    time: horaActual
                });

                this.loading = true;
                this.scrollToBottom();

                try {
                    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                    const token = tokenMeta ? tokenMeta.getAttribute('content') : '';

                    const formData = new FormData();
                    formData.append('mensaje', query);
                    formData.append('_token', token);
                    const historialSlice = this.mensajes.slice(-6).map(m => ({ role: m.role, content: m.content }));
                    historialSlice.forEach((item, i) => {
                        formData.append(`historial[${i}][role]`, item.role);
                        formData.append(`historial[${i}][content]`, item.content);
                    });

                    const res = await fetch('{{ route('asistente.chat') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await res.json();
                    if (data && data.success) {
                        this.mensajes.push({
                            role: 'assistant',
                            content: data.respuesta,
                            time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                        });
                        if (data.sugerencias && data.sugerencias.length) {
                            this.sugerencias = data.sugerencias;
                        }
                    } else {
                        this.mensajes.push({
                            role: 'assistant',
                            content: 'Disculpa, no pude procesar tu consulta en este momento. Por favor intenta de nuevo.',
                            time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                        });
                    }
                } catch (e) {
                    this.mensajes.push({
                        role: 'assistant',
                        content: 'Ocurrió un error al conectar con el asistente. Verifica tu conexión de red.',
                        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                    });
                } finally {
                    this.loading = false;
                    this.scrollToBottom();
                }
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const box = document.getElementById('chatbot-messages-container');
                    if (box) {
                        box.scrollTop = box.scrollHeight;
                    }
                });
            },

            renderMarkdown(text) {
                if (!text) return '';
                let safe = text
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');

                // Encabezados
                safe = safe.replace(/### (.*?)(?:\n|$)/g, '<h4 class="font-bold text-xs text-[#001229] dark:text-blue-300 mt-2 mb-1 flex items-center gap-1">$1</h4>');
                // Negritas
                safe = safe.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-slate-900 dark:text-white">$1</strong>');
                // Cursivas
                safe = safe.replace(/\*(.*?)\*/g, '<em class="italic text-slate-600 dark:text-slate-300">$1</em>');
                // Código / Formato
                safe = safe.replace(/`([^`]+)`/g, '<code class="px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-800 text-[10px] font-mono text-blue-600 dark:text-blue-400 border border-slate-200 dark:border-slate-700">$1</code>');
                // Enlaces
                safe = safe.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" class="text-[#0051d5] dark:text-blue-400 font-semibold underline hover:text-blue-700 transition-colors">$1</a>');
                // Listas viñetas
                safe = safe.replace(/^\* (.*?)$/gm, '<div class="flex items-start gap-1.5 my-0.5"><span class="text-blue-500 dark:text-blue-400 mt-0.5">•</span><span>$1</span></div>');
                // Listas numeradas
                safe = safe.replace(/^(\d+)\. (.*?)$/gm, '<div class="flex items-start gap-1.5 my-0.5"><span class="font-mono text-blue-600 dark:text-blue-400 font-bold">$1.</span><span>$2</span></div>');
                // Saltos de línea dobles y simples
                safe = safe.replace(/\n\n/g, '<div class="my-1.5"></div>');
                safe = safe.replace(/\n/g, '<br/>');

                return safe;
            }
        };
    }
</script>

