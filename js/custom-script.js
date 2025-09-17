var $ = jQuery.noConflict();

jQuery(document).ready(function () {
  $(".lesm-popular-education-list").slick({
    dots: false,
    // infinite: true,
    // speed: 300,
    slidesToShow: 4,
    slidesToScroll: 1,
    prevArrow:
      '<button type="button" class="slick-prev slick-arrow"><img src="/wp-content/themes/salient-child/images/arrow.png"></button>',
    nextArrow:
      '<button type="button" class="slick-next slick-arrow"><img src="/wp-content/themes/salient-child/images/arrow.png"></button>',
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
        },
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
        },
      },
    ],
  });

  $(".btn-event-registration").click(function (e) {
    e.preventDefault();
    $(".lesm-events-submission-popup").addClass("open");
  });

  $(".lesm-events-submission-popup span.popup-close").click(function () {
    $(".lesm-events-submission-popup").removeClass("open");
  });

  //   $("#lesm-library-form").on("change", function (e) {
  //     e.preventDefault();
  //     console.log("Form changed");
  //     var formData = $(this).serialize();
  //     console.log("Form data:", formData);

  //     $.ajax({
  //       type: "POST",
  //       url: admin_ajax.wp_ajax_url, // Ensure admin_ajax_url is defined in your theme or plugin
  //       data: {
  //         action: "lesm_filter_library",
  //         form_data: formData,
  //       },
  //       success: function (response) {
  //         $education_items = response.data.content;
  //         $query_count = response.data.count;
  //         $(".result-count").text($query_count);
  //         console.log("Response received:", response);
  //         $(".education-library-wrapper").html(response.data.content);
  //       },
  //       error: function (xhr, status, error) {
  //         console.error("Error occurred:", status, error);
  //       },
  //     });
  //   });

  // Debounce function to prevent too many AJAX calls while typing
  function debounce(func, wait = 300) {
    let timeout;
    return function () {
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(this, arguments), wait);
    };
  }

  // Shared AJAX function
  function fetchFilteredLibrary() {
    console.log("Form changed");
    var formData = $("#lesm-library-form").serialize();
    console.log("Form data:", formData);

    $.ajax({
      type: "POST",
      url: admin_ajax.wp_ajax_url,
      data: {
        action: "lesm_filter_library",
        form_data: formData,
      },
      success: function (response) {
        $(".result-count").text(response.data.count);
        $(".education-library-wrapper").html(response.data.content);
        console.log("Response received:", response);
        $("button#load-more-education").hide(); // Hide load more button after filtering
      },
      error: function (xhr, status, error) {
        console.error("Error occurred:", status, error);
      },
    });
  }

  // Trigger on checkbox or select dropdown changes
  $("#lesm-library-form").on(
    "change",
    "input[type='checkbox'], select",
    function () {
      fetchFilteredLibrary();
    }
  );

  // Trigger on typing in the text input (with debounce)
  $("#lesm-library-form").on(
    "input",
    "#education-title",
    debounce(function () {
      fetchFilteredLibrary();
    }, 300)
  );

  $("#load-more-education").on("click", function (e) {
    e.preventDefault();

    var $this = $(this);
    var currentPage = parseInt($this.attr("data-current-page"));
    var maxPages = parseInt($this.attr("data-max-pages"));
    var nextPage = currentPage + 1;
    var postsPerPage = parseInt($this.attr("data-post-per-page"));

    if (nextPage <= maxPages) {
      $.ajax({
        url: admin_ajax.wp_ajax_url,
        type: "POST",
        data: {
          action: "lesm_load_more_education",
          paged: nextPage,
          quantity: postsPerPage,
        },
        success: function (response) {
          console.log(response);
          if (response.success && response.data.html) {
            $(".education-library-wrapper").append(response.data.html);
            $this.attr("data-current-page", nextPage);

            // Optional: Update result count
            var $currentCount = parseInt($(".result-count").text());
            var countFromAjax = response.data.count || 0;
            $(".result-count").text($currentCount + countFromAjax);

            // Hide if last page
            if (nextPage >= maxPages) {
              $this.hide();
            }

            console.log("More education loaded successfully.");
          } else {
            console.warn("No more content.");
            $this.hide();
          }
        },
        error: function (xhr, status, error) {
          console.error("Error loading more education:", status, error);
        },
      });
    } else {
      $this.hide();
    }
  });
  $("#load-more-news").on("click", function (e) {
    e.preventDefault();

    var $btn = $(this); // ✅ store reference to the button
    var currentPage = parseInt($btn.attr("data-current-page"));
    var maxPage = parseInt($btn.attr("data-max-page"));
    var postPerPage = $btn.attr("data-post-per-page");

    if (currentPage < maxPage) {
      var nextPage = currentPage + 1;
      $.ajax({
        url: admin_ajax.wp_ajax_url,
        type: "POST",
        data: {
          action: "lesm_load_more_news",
          paged: nextPage,
          quantity: postPerPage,
        },
        success: function (response) {
          console.log(response);
          if (response.success && response.data.html) {
            $(".lesm-events-gird.news").append(response.data.html);

            var newCurrentPage = response.data.current_page || nextPage;
            $btn.attr("data-current-page", newCurrentPage); // ✅ update using stored reference

            // Hide button if last page
            if (newCurrentPage >= maxPage) {
              $btn.hide();
            }

            console.log("More news loaded successfully.");
          } else {
            console.warn("No content returned.");
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX error:", error);
        },
      });
    } else {
      console.warn("No more content.");
      $btn.hide();
    }

    console.log("Current:", currentPage, "Max:", maxPage);
  });
});
