// src/js/favourites.ts

/**
 * Backwards compatible implementation of the favourites feature.
 * Removes use of jQuery
 */
export class FavouritesAdaptation {
    private static target = '.new-favourite-button';

    static bindFavouriteInteractions()
    {
        document.querySelectorAll(FavouritesAdaptation.target)?.forEach(f => {
            f.removeEventListener('click', FavouritesAdaptation.clickEvent);
            f.addEventListener('click', FavouritesAdaptation.clickEvent);
        })

    }

    private static clickEvent = async function() {
        const favourite_class = this.getAttribute('data-class');
        const favourite_id = this.getAttribute('data-id');

        const response = await fetch(`/favorite/ajaxEditFavorites?class=${favourite_class}&id=${favourite_id}`)

        // @todo: maybe send a response and check it?

        if (this.classList.contains('bi-star-fill')) {
            this.classList.remove('bi-star-fill');
            this.classList.add('bi-star');
        } else {
            this.classList.add('bi-star-fill');
            this.classList.remove('bi-star');
        }
    }
}
