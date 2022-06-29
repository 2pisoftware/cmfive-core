<?php

class AdminService extends DbService
{
    /**
     * Returns a country via the $id parameter.
     *
     * @param string $id
     * @return Country|null
     */
    public function getCountry(string $id): ?Country
    {
        return $this->getObject('Country', $id);
    }

    /**
     * Returns a country filtering on the $where parameter.
     *
     * @param array $where
     * @return Country|null
     */
    public function getCountryWhere(array $where): ?Country
    {
        return $this->getObject('Country', $where);
    }

    /**
     * Returns all countries filtered using the $where parameter.
     *
     * @param array $where
     * @return array
     */
    public function getCountries(array $where = []): array
    {
        return $this->getObjects('Country', $where);
    }

    /**
     * Returns an array of rows containing the country's demonym.
     *
     * @return array
     */
    public function getCountryDemonyms(): array
    {
        return $this->_db->get('country')
            ->select()
            ->select('demonym')
            ->where('is_deleted', 0)
            ->fetchAll();
    }

    /**
     * Returns an array of rows containing the country's name.
     *
     * @return array
     */
    public function getCountryNames(): array
    {
        return $this->_db->get('country')
            ->select()
            ->select('name')
            ->where('is_deleted', 0)
            ->fetchAll();
    }

    /**
     * Returns a country language via the $country_id and $language_id parameters.
     *
     * @param string $country_id
     * @param string $language_id
     * @return CountryLanguage|null
     */
    public function getCountryLanguage(string $country_id, string $language_id): ?CountryLanguage
    {
        return $this->getObject('CountryLanguage', [
            'country_id' => $country_id,
            'language_id' => $language_id,
        ]);
    }

    /**
     * Returns a language via the $id parameter.
     *
     * @param string $id
     * @return Language|null
     */
    public function getLanguage(string $id): ?Language
    {
        return $this->getObject('Language', $id);
    }

    /**
     * Returns a language filtering on the $where parameter.
     *
     * @param array $where
     * @return Language|null
     */
    public function getLanguageWhere(array $where): ?Language
    {
        return $this->getObject('Language', $where);
    }

    /**
     * Returns all languages filtered using the $where parameter.
     *
     * @param array $where
     * @return array
     */
    public function getLanguages(array $where = []): array
    {
        return $this->getObjects('Language', $where);
    }

    public function navigation(Web $w, $title = null, $prenav = null)
    {
        if ($title) {
            $w->ctx("title", $title);
        }

        $nav = $prenav ? $prenav : [];
        if (AuthService::getInstance($w)->loggedIn()) {
            $w->menuLink("admin/index", "Admin Dashboard", $nav);
            $w->menuLink("admin/users", "List Users", $nav);
            $w->menuLink("admin/groups", "List Groups", $nav);
            $w->menuLink('admin-maintenance', 'Maintenance', $nav);
            $w->menuLink("admin/lookup", "Lookup", $nav);
            $w->menuLink("admin-templates", "Templates", $nav);
            $w->menuLink("admin/composer", "Update composer.json", $nav, null, "_blank");
            $w->menuLink("admin-migration", "Migrations", $nav);
        }

        $w->ctx("navigation", $nav);
        return $nav;
    }
}
