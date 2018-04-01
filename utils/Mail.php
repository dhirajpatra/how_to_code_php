<?php
namespace Utils;

/**
 * Class Mail
 * @package utils
 */
class Mail
{
    private $settings;
    private $logger;

    /**
     * Mail constructor.
     * @param $settings
     * @param $logger
     */
    function __construct($settings, $logger)
    {
        $this->settings = $settings;
        $this->logger = $logger;
    }

    /**
     * @param $subject
     * @param null $sendto
     * @param $body
     * @param null $targetpath
     * @return mixed
     * @throws Exception
     */
    public function sendMail($subject, $sendto = null, $body, $targetpath = null)
    {

        $result = [];
        try {
            $mailer = $this->connectMail();
            $message = (new \Swift_Message())
                ->setFrom($this->settings['MAILER']['from'], 'Admin')
                ->setTo($sendto)
                ->setSubject($subject)
                ->setBody($body)
                ->setContentType('text/html')
            ;

            if (!empty($targetpath)) {
                $message->attach(\Swift_Attachment::fromPath($targetpath));
            }

            if ($result = $mailer->send($message)) {
                $this->logger->info('Mail sent to ' . json_encode($sendto) . __CLASS__ . __METHOD__);
            } else {
                $this->logger->error('Mail sent error' . __CLASS__ . __METHOD__);
            }

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $result;

    }

    /**
     * @return null|\Swift_Mailer
     * @throws Exception
     */
    private function connectMail()
    {

        $mailer = null;
        try {
            $transport = (new \Swift_SmtpTransport($this->settings['MAILER']['transport_agent'], $this->settings['MAILER']['secure_port'], $this->settings['MAILER']['secure_agent']))
                ->setUsername($this->settings['MAILER']['username'])
                ->setPassword($this->settings['MAILER']['password']);

            $mailer = new \Swift_Mailer($transport);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
            $this->logger->error('Mail connection error' . __CLASS__ . __METHOD__);
        }

        return $mailer;
    }
}