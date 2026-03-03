import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Organization/**/*.php',
        './resources/views/filament/organization/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
