<?php namespace Html\Cmfive;

use Html\GlobalAttributes;

enum DismissableAlertType {
    case Primary;
    case Secondary;
    case Info;
    case Warning;
    case Error;
}

/**
 * Class DismissableAlert
 * 
 * Represents a dismissable alert component that can be displayed to the user.
 * This class provides methods to set the alert content, type, and dismiss button.
 * It also handles the rendering of the alert HTML markup.
 * 
 * Note: requires Web instance to namespace the dismissKey
 */
class DismissableAlert extends \Html\Element
{
    use GlobalAttributes;

    public \Web $w;
    public DismissableAlertType $type = DismissableAlertType::Warning;
    public string $content = '';
    public string $dismissKey = '';
    
    /**
     * Sets the type of alert
     * 
     * @param DismissableAlertType $type
     * @return this
     */
    public function setType(DismissableAlertType $type): self {
        $this->type = $type;

        return $this;
    }

    public function setDismissKey(string $dismissKey): self {
        $this->dismissKey = $dismissKey;

        return $this;
    }

    public function setWeb(\Web $w): self {
        $this->w = $w;

        return $this;
    }

    /**
     * Gets the Bootstrap 5 class of the alert
     * 
     * @return string
     */
    private function getAlertClass(): string {
        return match ($this->type) {
            DismissableAlertType::Primary => 'alert-primary',
            DismissableAlertType::Secondary => 'alert-secondary',
            DismissableAlertType::Info => 'alert-info',
            DismissableAlertType::Warning => 'alert-warning',
            DismissableAlertType::Error => 'alert-danger',
        };
    }

    /**
     * Gets the Bootstrap 5 icon class for the alert
     * 
     * @return string
     */
    private function getAlertIcon(): string
    {
        return match ($this->type) {
            DismissableAlertType::Primary => 'bi-info-circle',
            DismissableAlertType::Secondary => 'bi-info-circle',
            DismissableAlertType::Info => 'bi-info-circle',
            DismissableAlertType::Warning => 'bi-exclamation-triangle',
            DismissableAlertType::Error => 'bi-exclamation-triangle',
        };
    }

    public function __toString(): string {
        $setting = \AuthService::getInstance($this->w)->getSettingByKey($this->w->_module . '__' . $this->dismissKey);
        if (!empty($setting->id)) {
            return '';
        }

        return <<<RETURN
<div class="alert {$this->getAlertClass()} {$this->class}" role="alert">
    <i class="bi {$this->getAlertIcon()} float-start me-2"></i>
    {$this->content}
    <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    <button type="button" class="btn btn-sm btn-outline-secondary float-end me-3" style="margin-top: -3px;" data-bs-dismiss="alert" onclick="fetch('/auth/ajax_set_setting?key={$this->w->_module}__{$this->dismissKey}&value=true')">Don't show this again</button>
</div>
RETURN;
    }
}