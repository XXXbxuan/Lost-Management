<x-mail::message>

<x-slot:header>
<x-mail::header :url="config('app.url')">
    <div style="text-align: center; font-weight: bold; font-size: 20px;">
        Airport Lost & Found System (ALIMS)
    </div>
</x-mail::header>
</x-slot:header>


# Email Verification Required

Hello,

Thank you for registering with **Airport Lost & Found System (ALIMS)**.

Please click the button below to verify your email address and activate your account.


<x-mail::button :url="$actionUrl" color="primary">
Verify Email Address
</x-mail::button>


If you did not create an account, no further action is required.

We appreciate your cooperation in keeping the system secure.


Regards,<br>
**Airport Lost & Found System (ALIMS)**


<x-slot:subcopy>

If you're having trouble clicking the "Verify Email Address" button, you may use the link below:

<a href="{{ $actionUrl }}" style="color: #1a73e8; text-decoration: underline;">
Click here to verify your email
</a>

</x-slot:subcopy>

</x-mail::message>