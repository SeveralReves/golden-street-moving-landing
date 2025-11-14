@extends('layouts.default')

@section('title', 'Terms & Conditions and Privacy Policy')

@section('content')


<section style="padding: 60px 20px; max-width: 960px; margin: 0 auto;">
    <h1 style="font-size: 48px; margin-bottom: 10px;">Terms &amp; Conditions &amp; Privacy Policy</h1>
    <p style="color: #555; margin-bottom: 40px;">
        Last updated: {{ date('Y') }}
    </p>

    {{-- ===================== TERMS & CONDITIONS ===================== --}}
    <section id="terms" style="margin-bottom: 60px;">
        <h2 style="font-size: 42px; margin-bottom: 15px;">Terms &amp; Conditions</h2>

        <p>
            Welcome to <strong>Golden Street Moving</strong>. These Terms &amp; Conditions (“Terms”) govern your use of our
            website, services, booking system, and any move or transportation service we provide (“Services”).
        </p>
        <p>
            By accessing our website or using our Services, you agree to be bound by these Terms.
        </p>

        <h3 style="margin-top: 25px;">1. Services Provided</h3>
        <p>
            <strong>Golden Street Moving</strong> offers residential and commercial moving services, packing and unpacking,
            loading and unloading, and out-of-state transportation depending on your location and availability.
            All Services are subject to scheduling and confirmation.
        </p>

        <h3 style="margin-top: 25px;">2. Quotes &amp; Estimates</h3>
        <p>
            All quotes provided through our website or by phone/email are <strong>estimates only</strong>.
            Final pricing may vary based on, but not limited to:
        </p>
        <ul style="margin-left: 20px; list-style: disc;">
            <li>Additional inventory not disclosed</li>
            <li>Unexpected access issues (stairs, long carries, elevators, restricted parking, etc.)</li>
            <li>Extra services requested</li>
            <li>Travel time, fuel, or toll fees</li>
        </ul>
        <p>
            The customer will be notified before any significant price adjustments whenever reasonably possible.
        </p>

        <h3 style="margin-top: 25px;">3. Scheduling &amp; Cancellations</h3>
        <p>
            Bookings are confirmed only after we contact you and approve the request.
        </p>
        <ul style="margin-left: 20px; list-style: disc;">
            <li>Cancellations should be made at least <strong>24 hours</strong> in advance.</li>
            <li>Last-minute cancellations may incur a cancellation fee.</li>
            <li>
                Delays caused by weather, traffic, or unforeseen circumstances do not constitute grounds for a refund,
                but we will make reasonable efforts to reschedule or complete the Service.
            </li>
        </ul>

        <h3 style="margin-top: 25px;">4. Customer Responsibilities</h3>
        <p>Customers are responsible for:</p>
        <ul style="margin-left: 20px; list-style: disc;">
            <li>Providing accurate information during booking</li>
            <li>Ensuring that pickup and delivery locations are accessible</li>
            <li>Securely packing all items unless packing services are purchased</li>
            <li>Informing movers of fragile, oversized, or high-value items</li>
        </ul>
        <p>
            <strong>Golden Street Moving</strong> is not responsible for items packed improperly by the customer.
        </p>

        <h3 style="margin-top: 25px;">5. Liability &amp; Damages</h3>
        <p>
            We take all reasonable precautions to protect your belongings. However, we are not liable for damages related to:
        </p>
        <ul style="margin-left: 20px; list-style: disc;">
            <li>Pre-existing structural or furniture weaknesses</li>
            <li>Items packed by the customer</li>
            <li>Electronic devices malfunctioning after transport</li>
            <li>Boxes that are not properly sealed or labeled</li>
        </ul>
        <p>
            High-value items (over $100 per pound) must be declared <strong>before the move</strong>.
            Additional insurance may be required for such items.
        </p>

        <h3 style="margin-top: 25px;">6. Insurance</h3>
        <p>
            Basic coverage may be included as required by applicable law. Additional moving insurance may be recommended
            for fragile or high-value items. Please contact us for details.
        </p>

        <h3 style="margin-top: 25px;">7. Prohibited Items</h3>
        <p>We do not transport, under any circumstances:</p>
        <ul style="margin-left: 20px; list-style: disc;">
            <li>Firearms or ammunition</li>
            <li>Hazardous materials or chemicals</li>
            <li>Perishable foods (for long-distance moves)</li>
            <li>Live animals</li>
            <li>Illegal substances</li>
        </ul>

        <h3 style="margin-top: 25px;">8. Payments</h3>
        <p>
            Payment is typically required upon completion of the move, unless otherwise agreed in writing.
            We may accept:
        </p>
        <ul style="margin-left: 20px; list-style: disc;">
            <li>Cash</li>
            <li>Credit/Debit Cards</li>
            <li>Bank Transfers (if arranged beforehand)</li>
        </ul>
        <p>
            Late or unpaid invoices may result in additional fees, interest, or legal collection efforts.
        </p>

        <h3 style="margin-top: 25px;">9. Website Use</h3>
        <p>
            You agree not to misuse or attempt to disrupt our website or booking system. All content, including text,
            images, logos, and design, is the property of <strong>Golden Street Moving</strong> or its licensors and
            is protected by applicable intellectual property laws.
        </p>

        <h3 style="margin-top: 25px;">10. Changes to These Terms</h3>
        <p>
            We may update these Terms at any time. The latest version will always be available on this page.
            Your continued use of the website or Services after changes indicates your acceptance of the updated Terms.
        </p>

        <h3 style="margin-top: 25px;">11. Contact Information</h3>
        <p>
            For any questions about these Terms &amp; Conditions, you can contact us at:
        </p>
        <p>
            Email: <a href="mailto:infogoldenstreets@gmail.com">infogoldenstreets@gmail.com</a><br>
            Phone: +1 (770) 589 9512
        </p>
    </section>

    {{-- ===================== PRIVACY POLICY ===================== --}}
    <section id="privacy" style="margin-bottom: 60px;">
        <h2 style="font-size: 42px; margin-bottom: 15px;">Privacy Policy</h2>

        <p>
            Your privacy is important to us. This Privacy Policy explains how <strong>Golden Street Moving</strong>
            we collects, uses, and protects your personal information when you visit our website
            and use our Services.
        </p>
        <p>
            By using our website, submitting a quote request, or booking our Services, you agree to the practices
            described in this Policy.
        </p>

        <h3 style="margin-top: 25px;">1. Information We Collect</h3>
        <p>We may collect the following types of information:</p>

        <h4 style="margin-top: 15px;">a) Personal Information</h4>
        <ul style="margin-left: 20px; list-style: disc;">
            <li>Name</li>
            <li>Email address</li>
            <li>Phone number</li>
            <li>Pickup and destination addresses</li>
            <li>Preferred move date and details</li>
            <li>Comments or notes you provide through forms</li>
        </ul>

        <h4 style="margin-top: 15px;">b) Automatically Collected Data</h4>
        <ul style="margin-left: 20px; list-style: disc;">
            <li>IP address</li>
            <li>Browser type and version</li>
            <li>Pages visited and time spent on the site</li>
            <li>Cookies and similar tracking technologies</li>
        </ul>

        <h3 style="margin-top: 25px;">2. How We Use Your Information</h3>
        <p>We use the information we collect to:</p>
        <ul style="margin-left: 20px; list-style: disc;">
            <li>Provide moving quotes and respond to inquiries</li>
            <li>Schedule, manage, and perform moving Services</li>
            <li>Communicate with you about your move or booking</li>
            <li>Improve our website, Services, and customer experience</li>
            <li>Maintain internal records and comply with legal obligations</li>
        </ul>

        <h3 style="margin-top: 25px;">3. How We Share Information</h3>
        <p>
            We do <strong>not</strong> sell or rent your personal information to third parties.
        </p>
        <p>We may share your information only with:</p>
        <ul style="margin-left: 20px; list-style: disc;">
            <li>Authorized employees and contractors who need it to provide Services</li>
            <li>Payment processors for billing and transactions</li>
            <li>Insurance providers if coverage or claims are involved</li>
            <li>Legal authorities, but only when required by law</li>
        </ul>

        <h3 style="margin-top: 25px;">4. Data Protection</h3>
        <p>
            We implement reasonable technical and organizational measures to protect your personal data.
            However, no method of transmission over the Internet or electronic storage is 100% secure.
            We cannot guarantee absolute security, but we strive to protect your information.
        </p>

        <h3 style="margin-top: 25px;">5. Cookies</h3>
        <p>
            Our website may use cookies and similar technologies to improve performance, analyze traffic,
            and enhance user experience. You can disable cookies through your browser settings, but some
            features of the website may not function properly as a result.
        </p>

        <h3 style="margin-top: 25px;">6. Your Rights</h3>
        <p>
            Depending on your state or jurisdiction, you may have certain rights regarding your personal information,
            including the right to:
        </p>
        <ul style="margin-left: 20px; list-style: disc;">
            <li>Request access to the personal data we hold about you</li>
            <li>Request correction of inaccurate information</li>
            <li>Request deletion of your personal data, subject to legal requirements</li>
            <li>Opt out of marketing communications</li>
        </ul>
        <p>
            To exercise these rights, please contact us at:
            <a href="mailto:infogoldenstreets@gmail.com">infogoldenstreets@gmail.com</a>.
        </p>

        <h3 style="margin-top: 25px;">7. Children’s Privacy</h3>
        <p>
            Our website and Services are not directed to children under the age of 13.
            We do not knowingly collect personal information from children. If you believe
            that a child has provided us with personal information, please contact us so
            we can delete it.
        </p>

        <h3 style="margin-top: 25px;">8. Third-Party Links</h3>
        <p>
            Our website may contain links to third-party websites. We are not responsible
            for the privacy practices or content of those external sites. We encourage
            you to review their privacy policies.
        </p>

        <h3 style="margin-top: 25px;">9. Changes to This Privacy Policy</h3>
        <p>
            We may update this Privacy Policy from time to time. The latest version will always be posted
            on this page with the “Last updated” date. Your continued use of the website or Services after
            changes are made constitutes your acceptance of the updated Policy.
        </p>

        <h3 style="margin-top: 25px;">10. Contact</h3>
        <p>
            For any questions or concerns about this Privacy Policy, you can contact us at:
        </p>
        <p>
            Email: <a href="mailto:infogoldenstreets@gmail.com">infogoldenstreets@gmail.com</a><br>
            Phone: +1 (770) 589 9512
        </p>
    </section>
</section>
@endsection