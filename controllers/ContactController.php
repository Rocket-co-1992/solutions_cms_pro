<?php

namespace Pandao\Controllers;

use Pandao\Common\Utils\MailUtils;
use Pandao\Common\Utils\DbUtils;
use Pandao\Services\SiteContext;
use Pandao\Models\DTO\Contact;
use Pandao\Models\Location;

class ContactController extends PageController
{
    /**
     * Displays the contact page with form and location data.
     * 
     * @param object $myPage The current page object.
     * @param bool $autoRender Whether to automatically render the view.
     */
    public function view($myPage, $autoRender = true)
    {
        parent::view($myPage, false);

        $this->viewData['contactForm'] = $this->viewData['contactForm'] ?? new Contact();
        $this->viewData = array_merge($this->viewData, [
            'field_notice' => $this->viewData['contactForm']->field_notice,
            'msg_error' => $this->viewData['contactForm']->error_msg,
            'msg_success' => $this->viewData['contactForm']->success_msg,
            'locations' => Location::getLocations($this->pms_db, $myPage->id)
        ]);

        $this->render($myPage->getPageTemplate());
    }

    /**
     * Handles the submission of the contact form.
     */
    public function submitForm()
    {
        $siteContext = SiteContext::get();
        
        $dto = new Contact($_POST);

        // Verify Google reCAPTCHA if configured
        if (PMS_CAPTCHA_PKEY != '' && PMS_CAPTCHA_SKEY != '') {
            $recaptcha_data = $this->verifyRecaptcha($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
            if (!$recaptcha_data->success || $recaptcha_data->score < 0.7) {
                $dto->field_notice['captcha'] = $siteContext->texts['INVALID_RECAPTCHA'];
            }
        }

        // Validate required fields
        if (empty($dto->name)) $dto->field_notice['name'] = $siteContext->texts['REQUIRED_FIELD'];
        if (empty($dto->email) || !preg_match('/^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$/i', $dto->email)) $dto->field_notice['email'] = $siteContext->texts['INVALID_EMAIL'];
        if (empty($dto->msg)) $dto->field_notice['msg'] = $siteContext->texts['REQUIRED_FIELD'];
        if (!$dto->privacy_agreement) $dto->field_notice['privacy_agreement'] = $siteContext->texts['REQUIRED_FIELD'];
        
        // If validation passed, insert message and send email
        if (empty($dto->field_notice) && empty($dto->captchaHoney)) {
            $data = [
                'id' => '',
                'name' => $dto->name,
                'address' => $dto->address,
                'phone' => $dto->phone,
                'email' => $dto->email,
                'subject' => $dto->subject,
                'msg' => $dto->msg,
                'add_date' => time(),
                'edit_date' => null
            ];

            $result_msg = DbUtils::dbPrepareInsert($this->pms_db, 'solutionsCMS_message', $data);
            if ($result_msg->execute()) {

                // Send confirmation email
                $mail = MailUtils::getMail($this->pms_db, 'CONTACT', [
                    '{name}' => $dto->name,
                    '{address}' => $dto->address,
                    '{phone}' => $dto->phone,
                    '{email}' => $dto->email,
                    '{msg}' => nl2br($dto->msg)
                ]);

                if ($mail !== false && MailUtils::sendMail(PMS_EMAIL, PMS_OWNER, $dto->subject, $mail['content'], $dto->email, $dto->name)) {
                    $dto->success_msg = $siteContext->texts['MAIL_DELIVERY_SUCCESS'];
                    $dto->reset();
                } else {
                    $dto->error_msg = $siteContext->texts['MAIL_DELIVERY_FAILURE'];
                }
            }
        } else {
            $dto->error_msg = $siteContext->texts['FORM_ERRORS'];
        }

        $this->viewData['contactForm'] = $dto;

        $this->view($siteContext->currentPage);
    }

    /**
     * Verifies the Google reCAPTCHA response.
     * 
     * @param string $response The reCAPTCHA response token.
     * @param string $remoteip The user's IP address.
     * @return object The decoded reCAPTCHA verification result.
     */
    private function verifyRecaptcha($response, $remoteip)
    {
        $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptcha_response = file_get_contents($recaptcha_url . '?secret=' . PMS_CAPTCHA_SKEY . '&response=' . $response . '&remoteip=' . $remoteip);
        return json_decode($recaptcha_response);
    }
}
