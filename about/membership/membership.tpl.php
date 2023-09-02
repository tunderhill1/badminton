<section id="main" class="wrapper">
    <div class="container">
        <header class="major special">
            <h2>Join Us</h2>
        </header>
        <div class="row">
            <div class="12u">
                <h4>Step 1: Purchase The Membership</h4>
                <p>This includes feather shuttles, clubs rackets (although most members bring their own), and all non-coaching sessions. Click <a href="https://www.imperialcollegeunion.org/activities/a-to-z/badminton" target="_blank">here</a> to purchase the membership securely via the union website. You must purchase an Imperial Athletes subscription first, after which you are eligible for IC Badminton membership. You should be able to purchase the Imperial Athletes subscription by clicking on 'More info' from the above link.</p>
                <h4 class="steps">Step 2: Login</h4>
                <p>You can then login to this website using the Imperial College username and password. Once the union payment is confirmed, your account will be activated automatically.</p>
                <div class="alert info">If your account is not automatically activated within 48 hours, please contact us via email at <a href="mailto:badminton@imperial.ac.uk">badminton@imperial.ac.uk</a>.</div>
            </div>
        </div>
    </div>
</section>
<div class="modal" id="cost">
    <div class="modal-content">
        <h4>Cost Breakdown</h4>
        For each of our many club sessions, it costs <b>£48 per hour</b> to book Ethos and an average of <b>£33</b> to supply shuttles.<br>
        <hr>
        The membership cost of £25 is heavily subsidised by Imperial College Union.
        <a href="#" class="close" onclick="closeCostModal()">&#10060;</a>
    </div>
</div>
<script>
    document.getElementById('cost').addEventListener('click', function(e) {
        e = window.event || e;
        if (this === e.target) {
            closeCostModal();
        }
    });

    function openCostModal() {
        document.getElementById('cost').classList.add('open');
    }

    function closeCostModal() {
        document.getElementById('cost').classList.remove('open');
    }
</script>
