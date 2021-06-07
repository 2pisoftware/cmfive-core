<?php

class TemplateService extends DbService
{
    /**
     *
     * @param in $id
     * @return Template
     */
    public function getTemplate($id)
    {
        return $this->getObject("Template", $id);
    }

    /**
     * Get a list of Template objects for module and category.
     *
     * @param string $module default null
     * @param string $category default null
     * @param string $includeInactive default false
     * @param string $includeDeleted default false
     * @return array(<<Template>>)
     */
    public function findTemplates($module = null, $category = null, $includeInactive = false, $includeDeleted = false)
    {
        if ($module) {
            $where['module'] = $module;
        }
        if ($category) {
            $where['category'] = $category;
        }
        if (!$includeInactive) {
            $where['is_active'] = 1;
        }
        if (!$includeDeleted) {
            $where['is_deleted'] = 0;
        }
        return $this->getObjects("Template", $where);
    }

    /**
     * Get the first Template object for module and category.
     *
     * @param string $module default null
     * @param string $category default null
     * @param string $includeInactive default false
     * @param string $includeDeleted default false
     * @return <<Template>>
     */
    public function findTemplate($module = null, $category = null, $includeInactive = false, $includeDeleted = false)
    {
        if ($module) {
            $where['module'] = $module;
        }
        if ($category) {
            $where['category'] = $category;
        }
        if (!$includeInactive) {
            $where['is_active'] = 1;
        }
        if (!$includeDeleted) {
            $where['is_deleted'] = 0;
        }
        return $this->getObject("Template", $where);
    }

    /**
     * Merging a template with data.
     *
     * For $template you can pass the following:
     *
     * 1) the ID of a Template object
     * 2) a Template object
     * 3) a path to a template file
     * 4) template code as a string
     *
     * @param int|Template|string $template
     * @param array $data
     * @return string
     */
    public function render($template, array $data)
    {
        if (empty($template)) {
            return;
        }

        // falling through the options:

        // if passing a template's id
        if (is_numeric($template)) {
            $template = $this->getTemplate($template);
            if ($template == null) {
                return;
            }
        }

        // if passing a Template object
        if (is_a($template, "Template")) {
            $template = $template->template_body;
        }

        // if passing a file path or string template
        if (is_string($template)) {
            if (file_exists($template)) {
                $dir = dirname($template);
                $loader = new Twig\Loader\FilesystemLoader($dir);
                $template = str_replace($dir . DIRECTORY_SEPARATOR, "", $template);
                $twig = new Twig\Environment($loader, ['debug' => true]);
                $twig->addExtension(new Twig\Extension\DebugExtension());
                return $twig->render($template, $data);
            } else {
                $loader = new Twig\Loader\ArrayLoader();
                $twig = new Twig\Environment($loader, ['debug' => true]);
                $twig->setCache(false);
                $template = $twig->createTemplate($template);
                return $template->render($data);
            }
        }
    }
}
