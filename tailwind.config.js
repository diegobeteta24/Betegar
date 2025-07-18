// tailwind.config.js
import defaultTheme from 'tailwindcss/defaultTheme'
import forms       from '@tailwindcss/forms'
import typography  from '@tailwindcss/typography'
import flowbite    from 'flowbite/plugin'

export default {
 presets: [
      
        require("./vendor/wireui/wireui/tailwind.config.js")
    ],


  content: [
    './resources/views/**/*.blade.php',
    './node_modules/flowbite/**/*.js',       // para que Tailwind escanee las clases de Flowbite
    './vendor/rappasoft/laravel-livewire-tables/resources/views/**/*.blade.php',
    './storage/framework/views/*.php',
    "./vendor/wireui/wireui/src/*.php",
        "./vendor/wireui/wireui/ts/**/*.ts",
        "./vendor/wireui/wireui/src/WireUi/**/*.php",
        "./vendor/wireui/wireui/src/Components/**/*.php",
    // etc...
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
    },
  },
  plugins: [
    forms,
    typography,
    flowbite,       // <— aquí el plugin
  ],
}
