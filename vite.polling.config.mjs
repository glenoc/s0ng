import { mergeConfig } from 'vite';
import config from './vite.config.mjs';

export default mergeConfig(config, {
    server: {
        watch: {
            usePolling: true,
        },
    },
});
