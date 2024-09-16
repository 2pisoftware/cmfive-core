<?php

class AdminService extends DbService
{
    /**
     * Returns a list of users for paginated tables
     *
     * @param array $where
     * @param integer|null $page_number
     * @param integer|null $page_size
     * @param string|null $sort
     * @param string|null $sort_direction
     * @return array
     */
    public function getUsers(array $where = [], ?int $page_number = null, ?int $page_size = null, ?string $sort = null, ?string $sort_direction = null): array
    {
        $query = $this->_db->get('user')->leftJoin('contact on user.contact_id = contact.id');

        foreach ($where as $key => $value) {
            $query->where($key, $value);
        }

        if (!empty($page_number) && !empty($page_size)) {
            $query->paginate($page_number, $page_size);
        }

        if (!empty($sort) && !empty($sort_direction)) {
            if ($sort == 'name') {
                $query->sort('contact.firstname', $sort_direction)->sort('contact.lastname', $sort_direction);
            } else {
                $query->sort($sort, $sort_direction);
            }
        }

        return $this->getObjectsFromRows('User', $query->fetchAll());
    }

    /**
     * Returns a count of users with a where clause
     *
     * @param array $where
     * @return integer
     */
    public function countUsers(array $where = []): int
    {
        return $this->w->db->get('user')->where($where)->count();
    }

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
     * Returns the country name represented by either alpha 2 or 3 code
     *
     * @param string $code
     * @param string $type
     * @return string
     */
    public function getCountryNameForISOCode(string $code, ?string $type = Country::_ALPHA_2_CODE): string
    {
        $query = $this->_db->get('country')
            ->select()
            ->select('name')
            ->where('is_deleted', 0);

        if ($type == Country::_ALPHA_2_CODE) {
            $query->where('alpha_2_code', $code);
        } else {
            $query->where('alpha_3_code', $code);
        }

        return $query->fetchElement('name');
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
        return $this->getObjects('Language', $where, false, true, 'name asc');
    }

    public function navigation(Web $w, $title = null, $prenav = null)
    {
        if ($title) {
            $w->ctx('title', $title);
        }

        $nav = $prenav ? $prenav : [];
        if (AuthService::getInstance($w)->loggedIn()) {
            $w->menuLink('admin/index', 'Admin Dashboard', $nav);
            $w->menuLink('admin/users', 'List Users', $nav);
            $w->menuLink('admin/groups', 'List Groups', $nav);
            $w->menuLink('admin-maintenance', 'Maintenance', $nav);
            $w->menuLink('admin-lookups', 'Lookups', $nav);
            $w->menuLink('admin-templates', 'Templates', $nav);
            $w->menuLink('admin-migration', 'Migrations', $nav);
        }

        $w->ctx("navigation", $nav);
        return $nav;
    }

    /**
     * @return MenuLinkStruct[]
     */
    public function navList(): array
    {
        return [
            new MenuLinkStruct('Admin Dashboard', 'admin/index'),
            new MenuLinkStruct('List Users', 'admin/users'),
            new MenuLinkStruct('List Groups', 'admin/groups'),
            new MenuLinkStruct('Maintenance', 'admin-maintenance'),
            new MenuLinkStruct('Lookups', 'admin-lookups'),
            new MenuLinkStruct('Templates', 'admin-templates'),
            new MenuLinkStruct('Migrations', 'admin-migration'),
        ];
    }
}
