<?php

use App\Mail\ContactSubmitted;
use Illuminate\Support\Facades\Mail;

test('contact page renders successfully', function () {
    $response = $this->get(route('contact'));
    $response->assertSuccessful();
});

test('contact form submission validates input fields', function () {
    $response = $this->post(route('contact.send'), []);
    $response->assertSessionHasErrors(['name', 'email', 'phone', 'subject', 'message']);
});

test('contact form submission sends email successfully and redirects back', function () {
    Mail::fake();

    $data = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '08123456789',
        'subject' => 'Booking Question',
        'message' => 'Hello, I have a question about booking rooms.',
    ];

    $response = $this->post(route('contact.send'), $data);
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Thank you! Your message has been sent successfully.');

    Mail::assertSent(ContactSubmitted::class, function (ContactSubmitted $mail) use ($data) {
        return $mail->hasTo('bagusguesthouse01@gmail.com') &&
               $mail->data['name'] === $data['name'] &&
               $mail->data['email'] === $data['email'] &&
               $mail->data['phone'] === $data['phone'] &&
               $mail->data['subject'] === $data['subject'] &&
               $mail->data['message'] === $data['message'];
    });
});
