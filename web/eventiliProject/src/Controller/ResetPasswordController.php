<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Swift_Image;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Display & process form to request a password reset.
     */
    #[Route('', name: 'app_forgot_password_request')]
    public function request(Request $request, Swift_Mailer $mailer, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $mailer,
                $form->get('email')->getData(),
                $translator
            );
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    /**
     * Confirmation page after a user has requested a password reset.
     */
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        // Generate a fake token if the user does not exist or someone hit this page directly.
        // This prevents exposing whether or not a user was found with the given email address or not
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }
        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     */
    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, TranslatorInterface $translator, string $token = null): Response
    {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE, [], 'ResetPasswordBundle'),
                $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            // Encode(hash) the plain password, and set it.
            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setMdp($encodedPassword);
            $this->entityManager->flush();
            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('app_personne_verif');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    
    #[Route('/reset', name: 'app_user_resett', methods: ['GET', 'POST'])]
    public function resett(Request $request): Response
    {
        
        return $this->renderForm('reset_password/email.html.twig');


    }
   
   
    

    
    private function processSendingPasswordResetEmail(Swift_Mailer $mailer,string $emailFormData, TranslatorInterface $translator): RedirectResponse
    {
        $user = $this->entityManager->getRepository(Personne::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return $this->redirectToRoute('app_check_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
            // $this->addFlash('reset_password_error', sprintf(
            //     '%s - %s',
            //     $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_HANDLE, [], 'ResetPasswordBundle'),
            //     $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            // ));

            return $this->redirectToRoute('app_check_email');
        }
        $url = $this->generateUrl('app_reset_password', [
            'token' =>  $resetToken->getToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
        // Create the Transport
$email = (new TemplatedEmail())
        ->from(new Address('yesmineguesmi@gmail.com', 'Evëntili'))
        ->to($user->getEmail())
        ->subject('Your password reset request')
        ->htmlTemplate('reset_password/email.html.twig')
        ->context([
            'resetToken' => $resetToken,
        ]);
$transport = (new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
->setUsername('yesmineguesmi@gmail.com')
->setPassword('oyjdjatabndjaaxg');

// Create the Mailer using your created Transport
$mailer = new Swift_Mailer($transport);

// Create a message
$message = (new Swift_Message('Reset Password'))
->setFrom(['yesmineguesmi@esprit.tn' => 'Evëntili'])
->setTo([$user->getEmail()])
->setBody("<p>Salut! </p>cliquez ici pour reset le password: " . $url, 'text/html');
// Joindre une image à l'e-mail
$image = Swift_Image::fromPath('C:\xamp2\htdocs\img\wedding.png');
$image1 = Swift_Image::fromPath('C:\xamp2\htdocs\img\logo.png');
$body = '<img src="' . $message->embed($image1) . '" style="max-width:29%;height:auto;">';
$body .= '<img src="' . $message->embed($image) . '" style="max-width:100%;height:auto;">';
$body .= '<br><br><br>';
$body .= '<p style="font-family: Comic Sans MS, cursive;margin-left: 288px;">Veuillez cliquer sur le bouton ci-dessous pour activer votre compte :</p>';
$body .= '<a href="' . $url . '" style="display:inline-block;background-color:#f58922;margin-left: 398px;color:#fff;padding:19px 35px;text-decoration:none;border-radius:5px;">Activer mon compte</a>';

// Ajouter l'image au contenu du message
$message->setBody($body, 'text/html');

// Send the message
$result = $mailer->send($message);

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('app_check_email');
    }
}
