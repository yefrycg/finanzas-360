<section id="benefits" class="scroll-mt-24 bg-white py-16 dark:bg-gray-950">
  <div class="mx-auto max-w-7xl px-4 lg:px-6">
    <x-ui.section-title title="¿Qué beneficios obtienes?"
      subtitle="Descubre cómo nuestro sistema puede transformar la forma en que gestionas tu dinero." />

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <x-ui.card title="Control total de tu dinero"
        description="Conoce exactamente en qué estás gastando y cómo administrar mejor tus ingresos.">
        <x-slot name="icon">
          <svg class="h-6 w-6 text-primary-700 dark:text-primary-300" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
            xmlns="http://www.w3.org/2000/svg">
            <path d="M4 6h16" />
            <path d="M4 12h16" />
            <path d="M4 18h16" />
            <circle cx="9" cy="6" r="2" />
            <circle cx="15" cy="12" r="2" />
            <circle cx="9" cy="18" r="2" />
          </svg>
        </x-slot>
      </x-ui.card>

      <x-ui.card title="Visualización clara"
        description="Gráficos simples que te ayudan a entender tu situación financiera.">
        <x-slot name="icon">
          <svg class="h-6 w-6 text-primary-700 dark:text-primary-300" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
            xmlns="http://www.w3.org/2000/svg">
            <path d="M4 19V5" />
            <path d="M4 19h16" />
            <path d="M8 19V11" />
            <path d="M12 19V7" />
            <path d="M16 19V14" />
            <path d="M20 19V9" />
          </svg>
        </x-slot>
      </x-ui.card>

      <x-ui.card title="Interfaz intuitiva"
        description="Interfaz intuitiva diseñada para que registres tus movimientos en segundos.">
        <x-slot name="icon">
          <svg class="h-6 w-6 text-primary-700 dark:text-primary-300" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
            xmlns="http://www.w3.org/2000/svg">
            <path d="M6 4l9 9-4 1 1 4-2 1-1-4-4 1V4z" />
          </svg>
        </x-slot>
      </x-ui.card>

      <x-ui.card title="Análisis financiero"
        description="Analiza tu comportamiento financiero y mejora tus hábitos de gasto.">
        <x-slot name="icon">
          <svg class="h-6 w-6 text-primary-700 dark:text-primary-300" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
            xmlns="http://www.w3.org/2000/svg">
            <circle cx="11" cy="11" r="6" />
            <path d="M20 20l-3.5-3.5" />
            <path d="M8.5 12.5l2-2 2 2 3-3" />
          </svg>
        </x-slot>
      </x-ui.card>
    </div>
  </div>
</section>
