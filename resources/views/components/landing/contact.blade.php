<section id="contact" class="scroll-mt-24 bg-white py-16 dark:bg-gray-950">
  <div class="mx-auto max-w-3xl px-4 lg:px-6">
    <h2 class="mb-4 text-center text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white">Contacto</h2>
    <p class="mb-10 text-center font-light text-gray-600 dark:text-gray-300 sm:text-xl">Estamos aquí para ayudarte. Ponte
      en contacto con nosotros.</p>

    <form action="#"
      class="space-y-6 rounded-3xl border border-gray-200/70 bg-gray-50/70 p-6 shadow-sm dark:border-gray-800/70 dark:bg-gray-900/30 sm:p-8">
      <div>
        <label for="email" class="mb-2 block text-sm font-medium text-gray-900 dark:text-gray-300">Correo
          electrónico</label>
        <input type="email" id="email"
          class="block w-full rounded-xl border border-gray-200 bg-white/80 p-3 text-sm text-gray-900 shadow-sm transition-all duration-200 focus:border-primary-400 focus:outline-none focus:ring-4 focus:ring-primary-200/60 dark:border-gray-800 dark:bg-gray-950/40 dark:text-white dark:placeholder-gray-500 dark:focus:border-primary-600 dark:focus:ring-primary-900/40"
          placeholder="jhondo@example.com" required>
      </div>

      <div>
        <label for="subject" class="mb-2 block text-sm font-medium text-gray-900 dark:text-gray-300">Asunto</label>
        <input type="text" id="subject"
          class="block w-full rounded-xl border border-gray-200 bg-white/80 p-3 text-sm text-gray-900 shadow-sm transition-all duration-200 focus:border-primary-400 focus:outline-none focus:ring-4 focus:ring-primary-200/60 dark:border-gray-800 dark:bg-gray-950/40 dark:text-white dark:placeholder-gray-500 dark:focus:border-primary-600 dark:focus:ring-primary-900/40"
          placeholder="Déjanos saber cómo podemos ayudarte" required>
      </div>

      <div class="sm:col-span-2">
        <label for="message" class="mb-2 block text-sm font-medium text-gray-900 dark:text-gray-400">Mensaje</label>
        <textarea id="message" rows="6"
          class="block w-full resize-none rounded-xl border border-gray-200 bg-white/80 p-3 text-sm text-gray-900 shadow-sm transition-all duration-200 focus:border-primary-400 focus:outline-none focus:ring-4 focus:ring-primary-200/60 dark:border-gray-800 dark:bg-gray-950/40 dark:text-white dark:placeholder-gray-500 dark:focus:border-primary-600 dark:focus:ring-primary-900/40"
          placeholder="Déjanos un mensaje..."></textarea>
      </div>

      <x-ui.button type="submit">Enviar mensaje</x-ui.button>
    </form>
  </div>
</section>
