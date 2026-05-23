<section id="home"
  class="relative scroll-mt-24 overflow-hidden bg-linear-to-b from-primary-50/70 via-white to-white dark:from-gray-950 dark:via-gray-950 dark:to-gray-900">
  <div class="pointer-events-none absolute inset-0" aria-hidden="true">
    <div class="absolute -top-28 left-1/2 h-80 w-80 -translate-x-1/2 rounded-full bg-primary-200/50 blur-3xl"></div>
    <div class="absolute -bottom-24 -left-24 h-80 w-80 rounded-full bg-primary-100/70 blur-3xl dark:bg-primary-950/40">
    </div>
  </div>

  <div
    class="relative mx-auto grid max-w-7xl gap-10 px-4 py-14 lg:grid-cols-12 lg:items-center lg:gap-8 lg:px-6 lg:py-20">
    <div class="mr-auto place-self-center lg:col-span-7">
      <h1
        class="mb-4 max-w-2xl text-4xl font-extrabold leading-tight tracking-tight text-gray-900 motion-safe:animate-fade-up [animation-delay:50ms] md:text-5xl xl:text-6xl dark:text-white">
        Gestiona tu dinero sin complicaciones</h1>
      <p
        class="mb-8 max-w-2xl text-base font-light leading-relaxed text-gray-600 motion-safe:animate-fade-up [animation-delay:140ms] md:text-lg lg:text-xl dark:text-gray-300">
        Lleva el control de tus ingresos y gastos, visualiza tus finanzas en tiempo real y toma mejores decisiones
        económicas.</p>

      @auth
        <x-ui.button :href="route('dashboard.index')" class="motion-safe:animate-fade-up [animation-delay:220ms]">Ir al
          Dashboard</x-ui.button>
      @else
        <x-ui.button :href="route('register')" class="motion-safe:animate-fade-up [animation-delay:220ms]">Comienza
          ahora</x-ui.button>
      @endauth
    </div>

    <div class="hidden lg:col-span-5 lg:flex lg:justify-end">
      <img
        class="w-full max-w-md drop-shadow-xl motion-safe:animate-[fade-in_700ms_var(--ease-out)_both,float_6s_var(--ease-in-out)_infinite]"
        src="{{ asset('img/hero.png') }}" alt="mockup">
    </div>
  </div>
</section>
