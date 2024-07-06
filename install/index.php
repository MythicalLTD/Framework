<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.min.css">
  <title>MythicalFramework Installation</title>
  <style>
    body {
      font-family: 'Nunito', sans-serif;
    }

    #particles-js {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
    }
  </style>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap');
  </style>

</head>

<body>
  <div id="particles-js"></div>
  <div class="min-h-screen flex items-center justify-center bg-zinc-900 dark:bg-zinc-900 custom">
    <div class="bg-zinc-800 dark:bg-zinc-800 rounded-lg p-8 shadow-lg max-w-md w-full animate__animated animate__fadeIn"
      style="position: absolute;">
      <h2 class="text-2xl font-bold text-white dark:text-zinc-200 mb-4 text-center">MythicalFramework</h2>
      <p class="text-white dark:text-zinc-200 mb-4 text-center">Please fill in the following details to proceed with the
        installation:</p>
      <form id="mysqlSetupForm">
        <div class="mb-4 flex items-center">
          <div class="w-3/4 mr-2">
            <label for="host" class="block text-white dark:text-zinc-200">Host:</label>
            <input type="text" id="host" name="host"
              class="w-full bg-zinc-700 dark:bg-zinc-700 text-black dark:text-white p-2 rounded-lg focus:outline-none focus:ring focus:ring-purple-600"
              value="127.0.0.1" required>
          </div>
          <div class="w-1/4 ml-2">
            <label for="port" class="block text-white dark:text-zinc-200">Port:</label>
            <input type="text" id="port" name="port"
              class="w-full bg-zinc-700 dark:bg-zinc-700 text-black dark:text-white p-2 rounded-lg focus:outline-none focus:ring focus:ring-purple-600"
              value="3306" required>
          </div>
        </div>
        <div class="mb-4">
          <label for="username" class="block text-white dark:text-zinc-200">Username:</label>
          <input type="text" id="username" name="username"
            class="w-full bg-zinc-700 dark:bg-zinc-700 text-black dark:text-white p-2 rounded-lg focus:outline-none focus:ring focus:ring-purple-600"
            required>
        </div>
        <div class="mb-4">
          <label for="password" class="block text-white dark:text-zinc-200">Password:</label>
          <input type="password" id="password" name="password"
            class="w-full bg-zinc-700 dark:bg-zinc-700 text-black dark:text-white p-2 rounded-lg focus:outline-none focus:ring focus:ring-purple-600"
            required>
        </div>
        <div class="mb-4">
          <label for="database" class="block text-white dark:text-zinc-200">Database Name:</label>
          <input type="text" id="database" name="name"
            class="w-full bg-zinc-700 dark:bg-zinc-700 text-black dark:text-white p-2 rounded-lg focus:outline-none focus:ring focus:ring-purple-600"
            value="framework" required>
        </div>
        <br>
        <button type="submit"
          class="w-full bg-gradient-to-r from-pink-500 to-purple-600 text-white dark:text-zinc-200 p-2 rounded-lg transition-colors hover:bg-gradient-to-r hover:from-pink-600 hover:to-purple-700">Install</button>
      </form>
    </div>
  </div>
</body>
<script>

  // Configuration for particles.js
  particlesJS("particles-js",
    {
      "particles": {
        "number": { "value": 160, "density": { "enable": true, "value_area": 800 } },
        "color": { "value": "#ffffff" },
        "shape": { "type": "circle", "stroke": { "width": 0, "color": "#000000" }, "polygon": { "nb_sides": 5 }, "image": { "src": "img/github.svg", "width": 100, "height": 100 } },
        "opacity": { "value": 1, "random": true, "anim": { "enable": true, "speed": 1, "opacity_min": 0, "sync": false } },
        "size": { "value": 3, "random": true, "anim": { "enable": false, "speed": 4, "size_min": 0.3, "sync": false } },
        "line_linked": { "enable": false, "distance": 150, "color": "#ffffff", "opacity": 0.4, "width": 1 },
        "move": { "enable": true, "speed": 1, "direction": "none", "random": true, "straight": false, "out_mode": "out", "bounce": false, "attract": { "enable": false, "rotateX": 600, "rotateY": 600 } }
      },
      "interactivity": {
        "detect_on": "canvas",
        "events": {
          "onhover": { "enable": true, "mode": "bubble" },
          "onclick": { "enable": true, "mode": "repulse" },
          "resize": true
        },
        "modes": {
          "grab": { "distance": 400, "line_linked": { "opacity": 1 } },
          "bubble": { "distance": 250, "size": 0, "duration": 2, "opacity": 0, "speed": 3 },
          "repulse": { "distance": 400, "duration": 0.4 },
          "push": { "particles_nb": 4 },
          "remove": { "particles_nb": 2 }
        }
      },
      "retina_detect": true
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
  /**
   * Default values for every alert!
   * 
   * Change this if you want to change the framework!
   * 
   */
  var popUpClass = "animate__animated animate__fadeInUp animate__faster";
  var popDownClass = "animate__animated animate__fadeOutDown animate__faster";
  var support_link = "https://discord.mythicalsystems.xyz";
  var default_logo = "https://avatars.githubusercontent.com/u/117385445";
  var default_name = "MythicalSystems";
  var default_seo_description= "MythicalSystems framework is one of the best php framework with support of plugins and themes built in and easy developer integration!";
  var default_seo_keywords = "MythicalSystems, Framework, php, php framework, mythicalsystemsphp, phpmythical, mythicalphp, mythicalframework, mythicframework , freephpframework, lavarel, phpfrm";
  var default_app_timezone = "UTC";

  $(document).ready(function () {
    $("#mysqlSetupForm").submit(function (event) {
      event.preventDefault();
      var formData = $(this).serialize();
      $.ajax({
        type: "GET",
        url: "/mysql",
        data: formData,
        success: function (response) {
          if (response === "OK") {
            Swal.fire({
              title: "MySQL Connection",
              text: "We successfully connect to the database server!",
              icon: "success",
              footer: '<a href="'+support_link+'" target="_new">Need help?</a>',
              showClass: {
                popup: popUpClass
              },
              hideClass: {
                popup: popDownClass
              }
            });

            Swal.fire({
              title: "Let's name your app!",
              html: "Here you can enter a nice name that will be displayed everywhere on your app!",
              input: "text",
              footer: '<a href="'+support_link+'" target="_new">Need help?</a>',
              icon: "question",
              inputAttributes: {
                autocapitalize: "off"
              },
              showCancelButton: true,
              confirmButtonText: "Submit",
              showLoaderOnConfirm: true,
              showClass: {
                popup: popUpClass
              },
              hideClass: {
                popup: popDownClass
              },
              preConfirm: async (settings_app_name) => {
                if (settings_app_name == "") {
                  settings_app_name = default_name;
                }
                Swal.fire({
                  title: "Let's setup your timezone",
                  html: "Find a list of supported timezones <a href='https://www.php.net/manual/en/timezones.php'>here</a>",
                  input: "text",
                  icon: "question",
                  footer: '<a href="'+support_link+'" target="_new">Need help?</a>',
                  inputAttributes: {
                    autocapitalize: "off"
                  },
                  showCancelButton: true,
                  confirmButtonText: "Submit",
                  showLoaderOnConfirm: true,
                  showClass: {
                    popup: popUpClass
                  },
                  hideClass: {
                    popup: popDownClass
                  },
                  preConfirm: async (settings_app_timezone) => {
                    if (settings_app_timezone == "") {
                      settings_app_timezone = default_app_timezone;
                    }
                    Swal.fire({
                      title: "Let's setup your logo",
                      html: "If you don't have one just press submit",
                      icon: "question",
                      footer: '<a href="'+support_link+'" target="_new">Need help?</a>',
                      input: "text",
                      inputAttributes: {
                        autocapitalize: "off"
                      },
                      showClass: {
                        popup: popUpClass
                      },
                      hideClass: {
                        popup: popDownClass
                      },
                      showCancelButton: true,
                      confirmButtonText: "Submit",
                      showLoaderOnConfirm: true,
                      preConfirm: async (settings_app_logo) => {
                        if (settings_app_logo === "") {
                          settings_app_logo = default_logo;
                        }
                        Swal.fire({
                          title: "Let's setup your seo description",
                          html: "If you don't have one just press submit",
                          icon: "question",
                          footer: '<a href="'+support_link+'" target="_new">Need help?</a>',
                          input: "text",
                          inputAttributes: {
                            autocapitalize: "off"
                          },
                          showClass: {
                            popup: popUpClass
                          },
                          hideClass: {
                            popup: popDownClass
                          },
                          showCancelButton: true,
                          confirmButtonText: "Submit",
                          showLoaderOnConfirm: true,
                          preConfirm: async (settings_app_seo_description) => {
                            if (settings_app_seo_description === "") {
                              settings_app_seo_description = default_seo_description;
                            }
                            Swal.fire({
                              title: "Let's setup your seo keywords",
                              html: "If you don't have one just press submit",
                              input: "text",
                              inputAttributes: {
                                autocapitalize: "off"
                              },
                              icon: "question",
                              footer: '<a href="'+support_link+'" target="_new">Need help?</a>',
                              showClass: {
                                popup: popUpClass
                              },
                              hideClass: {
                                popup: popDownClass
                              },
                              showCancelButton: true,
                              confirmButtonText: "Submit",
                              showLoaderOnConfirm: true,
                              preConfirm: async (settings_app_seo_keywords) => {
                                if (settings_app_seo_keywords === "") {
                                  settings_app_seo_keywords = default_seo_keywords;
                                }
                                $.ajax({
                                  type: "GET",
                                  url: "/install",
                                  data: {
                                    app_name: settings_app_name,
                                    app_timezone: settings_app_timezone,
                                    app_logo: settings_app_logo,
                                    app_seo_description: settings_app_seo_description,
                                    app_seo_keywords: settings_app_seo_keywords,
                                    mysql: formData
                                  },
                                  success: function (response) {
                                    if (response === "OK") {
                                      Swal.fire({
                                        title: "Installation",
                                        text: "The installation was successful!",
                                        icon: "success",
                                        footer: '<a href="'+support_link+'" target="_new">Need help?</a>',
                                        showClass: {
                                          popup: popUpClass
                                        },
                                        hideClass: {
                                          popup: popDownClass
                                        },
                                      });
                                      window.location.href = "/";
                                    } else if (response === "OK_DEL_FIRST_INSTALL") {
                                      Swal.fire({
                                        title: "Installation",
                                        text: "The installation was successful! But you have to delete the FIRST_INSTALL file due to permission issues with PHP!",
                                        icon: "warning",
                                        footer: '<a href="'+support_link+'" target="_new">Need help?</a>',
                                        showClass: {
                                          popup: popUpClass
                                        },
                                        hideClass: {
                                          popup: popDownClass
                                        },
                                      });
                                    } else {
                                      Swal.fire({
                                        title: "Installation",
                                        text: "Failed to install the framework: " + response,
                                        icon: "error",
                                        footer: '<a href="'+support_link+'" target="_new">Need help?</a>',
                                        showClass: {
                                          popup: popUpClass
                                        },
                                        hideClass: {
                                          popup: popDownClass
                                        },

                                      });
                                    }
                                  },
                                  error: function (error) {
                                    Swal.fire({
                                      title: "Installation",
                                      text: "Failed to install the framework: " + error.statusText,
                                      icon: "error",
                                      footer: '<a href="'+support_link+'" target="_new">Need help?</a>',
                                      showClass: {
                                        popup: popUpClass
                                      },
                                      hideClass: {
                                        popup: popDownClass
                                      },
                                    });
                                  }
                                });
                              },
                            });
                          },
                        });
                      },
                    });
                  },
                });
              },
            });
          } else {
            Swal.fire({
              title: "MySQL Connection",
              text: "Failed to connect to the MySQL server: " + response,
              icon: "error",
              showClass: {
                popup: popUpClass
              },
              hideClass: {
                popup: popDownClass
              },
              footer: '<a href="'+support_link+'" target="_new">Need help?</a>'
            });
          }
        },
        error: function (error) {
          Swal.fire({
            title: "MySQL Connection",
            text: "Failed to connect to the MySQL server: " + error.statusText,
            icon: "error",
            showClass: {
              popup: popUpClass
            },
            hideClass: {
              popup: popDownClass
            },
            footer: '<a href="'+support_link+'" target="_new">Need help?</a>'
          });
        }
      });
    });
  });
</script>

</html>