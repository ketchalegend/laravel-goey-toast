@php
    $positionMap = [
        'top-right' => 'top: 1rem; right: 1rem;',
        'top-center' => 'top: 1rem; left: 50%; transform: translateX(-50%);',
        'top-left' => 'top: 1rem; left: 1rem;',
        'bottom-right' => 'bottom: 1rem; right: 1rem;',
        'bottom-center' => 'bottom: 1rem; left: 50%; transform: translateX(-50%);',
        'bottom-left' => 'bottom: 1rem; left: 1rem;',
    ];

    $stackStyle = $positionMap[$position] ?? $positionMap['top-right'];
@endphp


<div
    data-goey-toast-root
    x-data="window.goeyToastStack({
        initialToasts: @js($toasts),
        maxVisible: @js($maxVisible),
        animation: @js($animation),
    })"
    x-on:goey-toast.window="push($event.detail)"
    style="position: fixed; {{ $stackStyle }} z-index: {{ $zIndex }}; width: min(360px, calc(100vw - 2rem)); pointer-events: none;"
>
    <svg style="position: absolute; width: 0; height: 0;">
        <defs>
            <filter id="goey-toast-filter">
                <feGaussianBlur in="SourceGraphic" stdDeviation="8" result="blur" />
                <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 24 -10" result="goo" />
                <feComposite in="SourceGraphic" in2="goo" operator="atop" />
            </filter>
        </defs>
    </svg>

    <div style="display: grid; gap: 0.65rem; filter: url(#goey-toast-filter);">
        <template x-for="toast in visibleToasts" :key="toast.id">
            <article
                :data-toast-id="toast.id"
                :class="`goey-toast goey-toast--${toast.type} ${toast.spring ? 'goey-toast--spring' : 'goey-toast--smooth'}`"
                x-show="toast.visible"
                x-transition:enter="goey-toast-enter"
                x-transition:enter-start="goey-toast-enter-start"
                x-transition:enter-end="goey-toast-enter-end"
                x-transition:leave="goey-toast-leave"
                x-transition:leave-start="goey-toast-leave-start"
                x-transition:leave-end="goey-toast-leave-end"
                role="status"
                aria-live="polite"
                style="pointer-events: auto;"
            >
                <p class="goey-toast__title" x-show="toast.title" x-text="toast.title"></p>
                <p class="goey-toast__message" x-text="toast.message"></p>
                <p class="goey-toast__description" x-show="toast.description" x-text="toast.description"></p>
                <button
                    x-show="toast.dismissible"
                    type="button"
                    class="goey-toast__dismiss"
                    @click="remove(toast.id)"
                    aria-label="Dismiss"
                >
                    ×
                </button>
                <button
                    x-show="toast.action && toast.action.label"
                    type="button"
                    class="goey-toast__action"
                    @click="handleAction(toast)"
                    x-text="toast.action?.label"
                ></button>
                <div class="goey-toast__timer" :style="`animation-duration: ${toast.duration}ms;`"></div>
            </article>
        </template>
    </div>
</div>

<style>
    .goey-toast {
        position: relative;
        overflow: hidden;
        border-radius: 14px;
        padding: 0.9rem 2.5rem 0.9rem 1rem;
        color: #fff;
        font-weight: 600;
        box-shadow: 0 20px 40px -24px rgba(2, 6, 23, 0.9);
        backdrop-filter: blur(6px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        transform-origin: top right;
    }

    .goey-toast__title {
        font-size: 1.02rem;
        font-weight: 700;
        margin-bottom: 0.2rem;
    }

    .goey-toast__message {
        font-size: 1rem;
        line-height: 1.35rem;
    }

    .goey-toast__description {
        margin-top: 0.35rem;
        font-size: 0.98rem;
        font-weight: 500;
        line-height: 1.35rem;
        color: rgba(255, 255, 255, 0.92);
    }

    .goey-toast__action {
        margin-top: 0.55rem;
        border: 0;
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        border-radius: 999px;
        padding: 0.25rem 0.7rem;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
    }

    .goey-toast--success {
        background: linear-gradient(135deg, #047857, #10b981);
    }

    .goey-toast--info {
        background: linear-gradient(135deg, #1d4ed8, #38bdf8);
    }

    .goey-toast--warning {
        background: linear-gradient(135deg, #a16207, #f59e0b);
    }

    .goey-toast--danger {
        background: linear-gradient(135deg, #b91c1c, #ef4444);
    }

    .goey-toast__dismiss {
        position: absolute;
        top: 0.45rem;
        right: 0.6rem;
        border: 0;
        color: rgba(255, 255, 255, 0.95);
        background: transparent;
        font-size: 1.05rem;
        line-height: 1;
        cursor: pointer;
    }

    .goey-toast__timer {
        position: absolute;
        left: 0;
        bottom: 0;
        height: 3px;
        width: 100%;
        background: rgba(255, 255, 255, 0.7);
        transform-origin: left;
        animation-name: goey-toast-timer;
        animation-timing-function: linear;
        animation-fill-mode: forwards;
    }

    .goey-toast-enter {
        transition-property: opacity, transform;
        transition-duration: var(--goey-enter-duration, 460ms);
        transition-timing-function: var(--goey-enter-curve, cubic-bezier(0.175, 0.885, 0.32, 1.275));
    }

    .goey-toast-enter-start {
        opacity: 0;
        transform: translate3d(0, calc(var(--goey-enter-offset, 14px) * -1), 0) scale(var(--goey-enter-scale, 0.92));
    }

    .goey-toast-enter-end {
        opacity: 1;
        transform: translate3d(0, 0, 0) scale(1);
    }

    .goey-toast-leave {
        transition-property: opacity, transform;
        transition-duration: var(--goey-leave-duration, 230ms);
        transition-timing-function: var(--goey-leave-curve, cubic-bezier(0.4, 0, 0.2, 1));
    }

    .goey-toast--spring {
        --goey-enter-curve: var(--goey-spring-curve, cubic-bezier(0.175, 0.885, 0.32, 1.275));
    }

    .goey-toast--smooth {
        --goey-enter-curve: var(--goey-smooth-curve, cubic-bezier(0.4, 0, 0.2, 1));
    }

    .goey-toast-leave-start {
        opacity: 1;
        transform: translate3d(0, 0, 0) scale(1);
    }

    .goey-toast-leave-end {
        opacity: 0;
        transform: translate3d(0, -10px, 0) scale(0.9);
    }

    @keyframes goey-toast-timer {
        from {
            transform: scaleX(1);
        }

        to {
            transform: scaleX(0);
        }
    }
</style>

<script>
    if (typeof window.normalizeGoeyToastDetail === 'undefined') {
        window.normalizeGoeyToastDetail = function (payload) {
            if (!payload) {
                return null;
            }

            if (Array.isArray(payload) && payload.length > 0 && typeof payload[0] === 'object') {
                return payload[0];
            }

            if (typeof payload === 'object') {
                return payload;
            }

            if (typeof payload === 'string') {
                return { message: payload };
            }

            return null;
        };
    }

    if (typeof window.goeyToastStack === 'undefined') {
        window.goeyToastStack = function (config) {
            return {
                queue: [],
                maxVisible: Number(config.maxVisible ?? 4),
                animation: config.animation ?? {},

                init() {
                    this.applyAnimationVars();

                    const incoming = Array.isArray(config.initialToasts) ? config.initialToasts : [];

                    for (const toast of incoming) {
                        this.push(toast);
                    }
                },

                get visibleToasts() {
                    return this.queue.slice(0, this.maxVisible);
                },

                applyAnimationVars() {
                    const root = this.$root;

                    if (!root) {
                        return;
                    }

                    root.style.setProperty('--goey-enter-duration', `${Number(this.animation.enterDuration ?? 460)}ms`);
                    root.style.setProperty('--goey-leave-duration', `${Number(this.animation.leaveDuration ?? 230)}ms`);
                    root.style.setProperty('--goey-enter-offset', `${Number(this.animation.startOffset ?? 14)}px`);
                    root.style.setProperty('--goey-enter-scale', `${Number(this.animation.startScale ?? 0.92)}`);
                    root.style.setProperty('--goey-spring-curve', this.animation.springCurve ?? 'cubic-bezier(0.175, 0.885, 0.32, 1.275)');
                    root.style.setProperty('--goey-smooth-curve', this.animation.smoothCurve ?? 'cubic-bezier(0.4, 0, 0.2, 1)');
                    root.style.setProperty('--goey-leave-curve', this.animation.smoothCurve ?? 'cubic-bezier(0.4, 0, 0.2, 1)');
                },

                push(detail) {
                    if (!detail || typeof detail.message !== 'string' || detail.message.length === 0) {
                        return;
                    }

                    const toast = {
                        id: detail.id ?? `goey-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`,
                        type: detail.type ?? 'info',
                        title: detail.title ?? null,
                        message: detail.message,
                        description: detail.description ?? null,
                        action: detail.action ?? null,
                        duration: Number(detail.duration ?? 4500),
                        dismissible: detail.dismissible ?? true,
                        spring: detail.spring ?? Boolean(this.animation.springEnabled ?? true),
                        visible: true,
                    };

                    this.queue.unshift(toast);

                    window.setTimeout(() => {
                        this.remove(toast.id);
                    }, toast.duration);
                },

                handleAction(toast) {
                    const action = toast?.action ?? null;

                    if (!action) {
                        return;
                    }

                    if (typeof action.href === 'string' && action.href.length > 0) {
                        window.location.assign(action.href);
                    }

                    if (typeof action.event === 'string' && action.event.length > 0) {
                        window.dispatchEvent(new CustomEvent(action.event, {
                            detail: action.payload ?? {},
                        }));
                    }

                    if (action.dismissOnClick !== false) {
                        this.remove(toast.id);
                    }
                },

                remove(id) {
                    const index = this.queue.findIndex((toast) => toast.id === id);

                    if (index === -1) {
                        return;
                    }

                    this.queue[index].visible = false;

                    window.setTimeout(() => {
                        this.queue = this.queue.filter((toast) => toast.id !== id);
                    }, 260);
                },
            };
        };
    }

    if (typeof window.goeyToast === 'undefined') {
        window.goeyToast = function (message, options = {}) {
            window.dispatchEvent(new CustomEvent('goey-toast', {
                detail: {
                    message,
                    ...options,
                },
            }));
        };
    }

</script>
