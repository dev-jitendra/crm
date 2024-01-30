<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>

  <meta charset="utf-8" />

  <title>Insight Wave Solution Indore Contact details</title>
  <!-- favicon -->
  <?php require_once('./resources/favicon.php'); ?>
  <?php require_once('./resources/universal.php'); ?>
  <?php require_once('./resources/meta/properties/contact-us.php'); ?>
  <?php require_once('./resources/header.php'); ?>
  <script>
    window.onload = function() {
      document.getElementById("contact").className += " current";
    }
  </script>

</head>

<body>
  <?php include_once('./resources/navigation/nav.php'); ?>

  <!--Breadcrumb Area-->
  <section class="breadcrumb-area banner-3">
    <div class="text-block">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 v-center">
            <div class="bread-inner">
              <div class="bread-menu">
                <ul>
                  <li><a href="index.php">Home</a></li>
                  <li><a href="#">Contact Us</a></li>
                </ul>
              </div>
              <div class="bread-title">
                <h2>Contact Us</h2>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!--End Breadcrumb Area-->

  <!--Start Enquire Form-->
  <section class="enquire-form pad-tb">
    <div class="container">
      <div class="row">
        <div class="col-lg-6">
          <div class="common-heading text-l">
            <span>Contact Now</span>
            <h2 class="mt0">Have Question?<br />Inquire Now</h2><br><br>
          </div>
          <div class="form-block">
            <!--<form id="htmlformwizard1" action="contact-form.php" onSubmit="return validateForm()" novalidate="novalidate">-->
            <form id="htmlformwizard1" action="#" novalidate="novalidate">
              <div class="fieldsets row">
                <div class="col-md-6"><input id="name" type="text" placeholder="Full Name" name="name"></div>
                <div class="col-md-6"><input id="email" type="email" placeholder="Email Address" name="email"></div>
              </div>
              <div class="fieldsets row">
                <div class="col-md-6"><input id="phone" type="number" placeholder="Contact Number" name="phone"></div>
                <div class="col-md-6"><input id="sub" type="text" placeholder="Subject" name="sub"></div>
              </div>
              <div class="fieldsets"><textarea id="message" placeholder="Message" name="message"></textarea></div>

              </p>

              <div class="fieldsets mt20"> <button id="submit" type="submit" name="submit" value="Send" class="lnk btn-main bg-btn"> Submit <i class="fa fa-chevron-right fa-icon"></i><span class="circle"></span></button> </div>
            </form>



          </div>
        </div>

        <div class="col-lg-6 v-center">
          <div class="contact-details">
            <div class="email-card mt30">
              <div class="info-card v-center">
                <span><i class="fa fa-map-marker"></i> Address :</span>
                <div class="info-body">
                  <p><a href="index.php">Insight Wave Solutions</a> - Plot 402, MJ Business Park, Second Floor, Vijay Nagar, Indore, Madhya Pradesh 452010</p>
                </div>
              </div>
            </div><br>
            <div class="contact-card">
              <div class="info-card v-center">
                <span><i class="fa fa-phone"></i> Call :</span>
                <div class="info-body">
                  <p>Mon to Sat : 10 AM to 7 PM</p>
                  <a href="tel:+917772000213">+91 77720 00213</a><br><br>
                  <a href="tel:+917772000213">+91 77720 0xxxx</a>
                </div>
              </div>
            </div>
            <div class="email-card mt30">
              <div class="info-card v-center">
                <span><i class="fa fa-envelope"></i> Email :</span>
                <div class="info-body">
                  <p>Our team will get back to in 24 hour during business hours.</p>
                  <a href="mailto:info@insightdigiwave.com">info@insightdigiwave.com</a>
                </div>
              </div>
            </div>
            <div class="skype-card mt30">
              <div class="info-card v-center">
                <span><i class="fa fa-whatsapp"></i> Whatsapp :</span>
                <div class="info-body">
                  <p>We are online : 24 / 7</p>

                  <a href="https://wa.me/+917772000213?text=Hi,%20I%20am%20interested%20in%20your%20services.%20Please%20provide%20more%20details." target="_blank">+91 77720 00213</a>
                </div>
              </div>
            </div>
          </div>
        </div>


      </div>
    </div>
  </section>
  <!--End Enquire Form-->


  <!--Start Footer-->
  <?php
  require_once('./resources/footer.php');
  ?>
  <!--End Footer-->

</body>
</html>