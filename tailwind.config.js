// tailwind.config.js
import defaultTheme from 'tailwindcss/defaultTheme'
import forms       from '@tailwindcss/forms'
import typography  from '@tailwindcss/typography'
// flowbite plugin disabled for CI stability; re-enable after locking versions
// import flowbite    from 'flowbite/plugin'
import { createRequire } from 'module'

// Allow requiring CommonJS modules from ESM config
const require = createRequire(import.meta.url)

// Make WireUI preset optional (CI may not have vendor installed during assets build)
let wireuiPreset = null
try {
  require.resolve('./vendor/wireui/wireui/tailwind.config.js')
  wireuiPreset = require('./vendor/wireui/wireui/tailwind.config.js')
} catch (e) {
  wireuiPreset = null
}

export default {
  presets: [
    ...(wireuiPreset ? [wireuiPreset] : [])
  ],


  content: [
    './resources/views/**/*.blade.php',
  // './node_modules/flowbite/**/*.js',     // disabled in CI
    './vendor/rappasoft/laravel-livewire-tables/resources/views/**/*.blade.php',
    './storage/framework/views/*.php',
    // Include WireUI sources only if vendor exists
    ...(wireuiPreset ? [
      './vendor/wireui/wireui/src/*.php',
      './vendor/wireui/wireui/ts/**/*.ts',
      './vendor/wireui/wireui/src/WireUi/**/*.php',
      './vendor/wireui/wireui/src/Components/**/*.php',
    ] : []),
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
  // flowbite,     // disabled in CI
  ],
}
