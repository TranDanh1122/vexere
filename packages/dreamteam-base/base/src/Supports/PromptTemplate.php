<?php

namespace DreamTeam\Base\Supports;

class PromptTemplate
{
    protected $templates;

    public function __construct()
    {
        $this->templates = $this->loadTemplates();
    }

    public function loadTemplates()
    {
        return config('prompts.auto_setup');
    }

    public function getTemplate($templateName)
    {
        return $this->templates[$templateName] ?? null;
    }

    public function generatePrompt($templateName, $params)
    {
        $template = $this->getTemplate($templateName);

        if (!$template) {
            throw new \Exception("Template do'nt exists!");
        }

        foreach ($params as $key => $value) {
            if (is_array($value)) $value = json_encode($value);
            $template = str_replace("__" . $key . "__", $value, $template);
        }

        return $template;
    }
}
