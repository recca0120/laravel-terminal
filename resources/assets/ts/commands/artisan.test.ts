import { Artisan } from './artisan';

describe('Artisan', () => {
    it('run artisan', () => {
        const artisan = new Artisan();
        expect(artisan.run('artisan migrate --seed')).toEqual({});
    });
});
