<?php

namespace Pandao\Models\DTO;

class Contact
{
    public $name = '';
    public $email = '';
    public $address = '';
    public $phone = '';
    public $subject = '';
    public $msg = '';
    public $captchaHoney = '';
    public $privacy_agreement = false;
    public $field_notice = [];
    public $success_msg = '';
    public $error_msg = '';
    
    /**
     * Initializes a Contact object with form data.
     *
     * @param array $formData The data from the contact form.
     */
    public function __construct(array $formData = [])
    {
        $this->name = htmlspecialchars($formData['name'] ?? '', ENT_QUOTES, 'UTF-8');
        $this->email = htmlspecialchars($formData['email'] ?? '', ENT_QUOTES, 'UTF-8');
        $this->address = htmlspecialchars($formData['address'] ?? '', ENT_QUOTES, 'UTF-8');
        $this->phone = htmlspecialchars($formData['phone'] ?? '', ENT_QUOTES, 'UTF-8');
        $this->subject = htmlspecialchars($formData['subject'] ?? '', ENT_QUOTES, 'UTF-8');
        $this->msg = htmlspecialchars($formData['msg'] ?? '', ENT_QUOTES, 'UTF-8');
        $this->captchaHoney = htmlspecialchars($formData['captchaHoney'] ?? '', ENT_QUOTES, 'UTF-8');
        $this->privacy_agreement = isset($formData['privacy_agreement']);
    }

    /**
     * Resets the properties of the Contact DTO object to their default values.
     *
     * @return void
     */
    public function reset()
    {
        $this->name = '';
        $this->email = '';
        $this->address = '';
        $this->phone = '';
        $this->subject = '';
        $this->msg = '';
        $this->captchaHoney = '';
        $this->privacy_agreement = false;
    }
}
