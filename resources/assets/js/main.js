$.ajaxSetup({
  headers: {
    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
  },
});
const loadingBtn = `<svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20px" height="20px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
    <path fill="#fff" d="M25.251,6.461c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615V6.461z">
      <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"></animateTransform>
      </path>
    </svg>`;
$(document).ready(function () {
  $("body")
    .off()
    .on("click", ".menu-mobile-icon button", function () {
      if ($(".menu-mobile").hasClass("active")) {
        $(".menu-mobile").removeClass("active");
        $("body").removeClass("overflow-hidden");
      } else {
        $(".menu-mobile").addClass("active");
        $("body").addClass("overflow-hidden");
      }
    });
  $("body").on("click", ".menu-mobile-close", function () {
    $(".menu-mobile").removeClass("active");
    $("body").removeClass("overflow-hidden");
  });
  $("body").on("click", ".navigation-nav .icon-down", function () {
    $(this).closest("li").addClass("active");
    $(this).closest("li").find("> .submenu").slideToggle();
  });

  // LoadingBox
  function loadingBox(type = "open") {
    if (type == "open") {
      $("#loading_box")
        .css({ visibility: "visible", opacity: 0.0 })
        .animate({ opacity: 1.0 }, 200);
    } else {
      $("#loading_box").animate({ opacity: 0.0 }, 200, function () {
        $("#loading_box").css("visibility", "hidden");
      });
    }
  }
  window.loadingBox = loadingBox;
  function alert_show(type = "success", message = "") {
    $("#toast-container .toast").addClass("toast-" + type);
    $("#toast-container .toast-message_content").text(message);
    $("#toast-container").css("display", "block");
    setTimeout(function () {
      $("#toast-container").css("display", "none");
      $("#toast-container .toast").removeClass("toast-" + type);
      $("#toast-container .toast-message_content").text("");
    }, 6000);
  }
  window.alert_show = alert_show;
  //Tra cứu
  $("body").on("submit", ".tracuu-form", function (e) {

    e.preventDefault();
    const formData = {
      phone: $("#phone").val(),
      cccd: $("#cccd").val(),
    };
    const button = $(this).find("button[type='submit']");
    $.ajax({
      url: "/ajax/custormer-slot",
      type: "POST",
      dataType: "json",
      data: formData,
      beforeSend: function () {
        button.prepend(loadingBtn);
        button.attr("disabled", true);
        button.addClass("loading");
        button.closest("form").find(".error").text("");
      },
      success: function (response) {
        if (response.error) {
          if (response.type == "validation") {
            button.closest("form").find(".error").text(response.message);
          }
        } else {
        
          $("#popup-tracuu").addClass("active");
            $("#popup-tracuu").find(".popup-body").html(response.html);
          // alert_show("success", response.message);
          // $("#popup-booking").removeClass("active");
        }
        button.find("#loader-1").remove();
        button.attr("disabled", false);
        button.removeClass("loading");
      },
      error: function (e) {
        button.find("#loader-1").remove();
        button.attr("disabled", false);
        button.removeClass("loading");
        alert_show("error", e.message || "Có lỗi xảy ra, vui lòng thử lại sau");
      },
    });
  });
    $("body").on("click", "#close-popup-tracuu", function () {
    $("#popup-tracuu").removeClass("active");
  });
  // form search
  // Set up date pickers with flatpickr
  const today = new Date();
  // Function to get Vietnamese day of week label
  function getVietnameseDayOfWeek(date) {
    const days = ["CN", "T2", "T3", "T4", "T5", "T6", "T7"];
    return days[date.getDay()];
  }

  // Custom formatter for the date display
  function formatDateWithDayOfWeek(date) {
    if (!date) return "";
    const dayOfWeek = getVietnameseDayOfWeek(date);
    const day = date.getDate().toString().padStart(2, "0");
    const month = (date.getMonth() + 1).toString().padStart(2, "0");
    const year = date.getFullYear();
    return `${dayOfWeek}, ${day}/${month}/${year}`;
  }
  // Initialize with the values from the image

  const isMobile = window.matchMedia("(max-width: 768px)").matches;
  // Departure date picker
  const departureDatePicker = flatpickr("#departure-date", {
    dateFormat: "d/m/Y",
    minDate: "today",
    defaultDate: new Date(),
    showMonths: isMobile ? 1 : 2, // hiển thị 2 tháng
    locale: "vn", // dùng thứ tiếng Việt: T2, T3,...
    disableMobile: true, // luôn dùng desktop UI
    onChange: function (selectedDates) {
      if (selectedDates[0]) {
        const date = selectedDates[0];
        $("#departure-date").val(formatDateWithDayOfWeek(date));
        returnDatePicker.set("minDate", date);
        if (returnDatePicker.selectedDates[0]) {
          $("#return-date").val(
            formatDateWithDayOfWeek(returnDatePicker.selectedDates[0])
          );
        } else {
          $(".date-inputs").removeClass("active");
        }
      }
    },
  });

  // Return date picker
  const returnDatePicker = flatpickr("#return-date", {
    dateFormat: "d/m/Y",
    minDate: departureDatePicker.selectedDates[0] || "today",
    showMonths: isMobile ? 1 : 2,
    locale: "vn",
    disableMobile: true,
    onChange: function (selectedDates) {
      if (selectedDates[0]) {
        $(".date-inputs").addClass("active");
        $("#return-date").val(formatDateWithDayOfWeek(selectedDates[0]));
      }
    },
  });

  const departureInputValue = $("#departure-date").data("date");
  const departureDate = departureInputValue
    ? new Date(departureInputValue)
    : new Date();
  departureDatePicker.setDate(departureDate);
  // Set initial values with correct day of week
  $("#departure-date").val(formatDateWithDayOfWeek(departureDate));
  returnDatePicker.set("minDate", departureDate);

  const returnInputValue = $("#return-date").data("date");
  if (returnInputValue) {
    const returnDate = new Date(returnInputValue);
    returnDatePicker.setDate(returnDate);
    $("#return-date").val(formatDateWithDayOfWeek(returnDate));
  }

  // Swap locations functionality
  $("body").on("click", "#swap-locations", function () {
    const departureValue = $("#departure").val();
    const destinationValue = $("#destination").val();

    const departureDisplayValue = $("#departure").data("name");
    const destinationDisplayValue = $("#destination").data("name");

    // Swap hidden inputs
    $("#departure").val(destinationValue);
    $("#destination").val(departureValue);
    $("#departure").data("name", destinationDisplayValue);
    $("#destination").data("name", departureDisplayValue);

    // Swap displayed values
    $("#departure-display").val(destinationDisplayValue);
    $("#destination-display").val(departureDisplayValue);
  });

  $("body").on("click", ".add-date-return", function () {
    $(this).parent().find("input").trigger("click");
  });
  $("body").on("click", ".remove-date", function () {
    $("#return-date").val("");
    $(".date-inputs").removeClass("active");
  });

  // Form submission
  $("body").on("submit", "#booking-form", function (e) {
    e.preventDefault();
    const formData = {
      departure: $("#departure").val(),
      destination: $("#destination").val(),
      departureDate: $("#departure-date").val(),
      returnDate: $("#return-date").val(),
    };
    const button = $(this).find("button");
    $.ajax({
      url: "/ajax/find-slots",
      type: "POST",
      dataType: "json",
      data: formData,
      beforeSend: function () {
        button.prepend(loadingBtn);
        button.attr("disabled", true);
        button.addClass("loading");
      },
      success: function (response) {
        if (response.error) {
          alert_show("error", response.message);
          button.find("#loader-1").remove();
          button.attr("disabled", false);
          button.removeClass("loading");
        } else {
          window.location.href = response.redirectUrl;
        }
      },
      error: function (e) {
        button.find("#loader-1").remove();
        button.attr("disabled", false);
        button.removeClass("loading");
        alert_show("error", e.message || "Có lỗi xảy ra, vui lòng thử lại sau");
      },
    });
  });

  // search page filter
  function fetchFilteredResults(
    startMinutes,
    endMinutes,
    minPrice,
    maxPrice,
    type = "replace",
    filterUrl = ""
  ) {
    const fillter = {
      timeRange:
        formatTimeForDisplay(startMinutes) +
        " - " +
        formatTimeForDisplay(endMinutes),
      priceRange: formatCurrency(minPrice) + " - " + formatCurrency(maxPrice),
      selectedFillters: getSelectedCompanies(),
    };
    if (filterUrl == "") {
      filterUrl = new URL(window.location.href);
    }
    if (type == "replace") {
      loadingBox("open");
    }
    $.ajax({
      url: filterUrl,
      type: "GET",
      data: {
        startTime: formatTimeForDisplay(startMinutes),
        endTime: formatTimeForDisplay(endMinutes),
        minPrice: minPrice,
        maxPrice: maxPrice,
        selectedFillters: getSelectedCompanies(),
        type: type,
      },
      success: function (response) {
        if (!response.error) {
          // Update the filter display
          $(".content-right__top").html(response.topHtml);
          if (response.type === "replace") {
            $(".content-right__list").html(response.html);
          } else {
            $(".content-right__list").append(response.html);
          }
        }
        loadingBox("close");
      },
      error: function (error) {
        console.error("Error fetching results:", error);
        loadingBox("close");
      },
    });
  }

  // Helper function to format time for API calls and display
  function formatTimeForDisplay(minutes) {
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    return String(hours).padStart(2, "0") + ":" + String(mins).padStart(2, "0");
  }

  // Helper function to get selected companies
  function getSelectedCompanies() {
    const selected = [];
    $(".company-item input:checked").each(function () {
      selected.push($(this).attr("id"));
    });
    return selected;
  }

  // Helper function to format currency for display
  function formatCurrency(amount) {
    return (
      new Intl.NumberFormat("vi-VN", {
        style: "currency",
        currency: "VND",
        maximumFractionDigits: 0,
        currencyDisplay: "symbol",
      })
        .format(amount)
        .replace("₫", "")
        .trim() + " ₫"
    );
  }
  // Function to update price inputs based on slider values
  function updatePriceInputs(minPrice, maxPrice) {
    $("#min-price").val(formatCurrency(minPrice));
    $("#max-price").val(formatCurrency(maxPrice));
  }
  updatePriceInputs(0, 2000000);

  $("#price-slider").slider({
    range: true,
    min: 0,
    max: 2000000, // 2,000,000 VND
    values: [0, 2000000], // Default values (0 - 2,000,000 VND)
    step: 5000, // 5,000 VND increments
    slide: function (event, ui) {
      updatePriceInputs(ui.values[0], ui.values[1]);
    },
    stop: function (event, ui) {
      // Use setTimeout to delay the AJAX call by 400ms after slider stops
      clearTimeout(window.priceSliderTimeout);
      window.priceSliderTimeout = setTimeout(function () {
        // Get current time slider values
        const timeValues = $("#time-slider").slider("values");
        // Call AJAX function with current values
        fetchFilteredResults(
          timeValues[0],
          timeValues[1],
          ui.values[0],
          ui.values[1]
        );
      }, 400);
    },
  });

  // Initialize jQuery UI Slider
  $("#time-slider").slider({
    range: true,
    min: 0,
    max: 1440, // 24 hours in minutes
    values: [0, 1440], // Default values (0:00 - 24:00)
    step: 30, // 30-minute increments
    slide: function (event, ui) {
      updateTimeInputs(ui.values[0], ui.values[1]);
    },
    stop: function (event, ui) {
      // Use setTimeout to delay the AJAX call by 400ms after slider stops
      clearTimeout(window.sliderTimeout);
      window.sliderTimeout = setTimeout(function () {
        // Call AJAX function with current time values
        const priceValues = $("#price-slider").slider("values");
        fetchFilteredResults(
          ui.values[0],
          ui.values[1],
          priceValues[0],
          priceValues[1]
        );
      }, 400);
    },
  });

  // Initialize time inputs
  updateTimeInputs(0, 1440);

  // Function to update time inputs based on slider values
  function updateTimeInputs(startMinutes, endMinutes) {
    const formatTime = function (minutes) {
      const hours = Math.floor(minutes / 60);
      const mins = minutes % 60;
      return (
        String(hours).padStart(2, "0") + ":" + String(mins).padStart(2, "0")
      );
    };

    $("#start-time").val(formatTime(startMinutes));
    $("#end-time").val(formatTime(endMinutes));
  }

  // Toggle sections with smooth animation and icon rotation
  $("body").on("click", ".sidebar .button-toggle", function () {
    const button = $(this);
    button
      .closest(".sidebar-section")
      .find(".section-content")
      .slideToggle(300, function () {
        // Only toggle class after animation completes for smooth look
        const isVisible = $(this).is(":visible");
        button.find("svg").toggleClass("rotate-180");
      });
  });

  // Company search functionality
  $(".sidebar-section .search-input").on("input", function () {
    const searchTerm = $(this).val().toLowerCase().trim();
    $(this)
      .parent()
      .find(".company-item")
      .each(function () {
        const companyName = $(this).find("label").text().toLowerCase();
        if (companyName.includes(searchTerm)) {
          $(this).show();
        } else {
          $(this).hide();
        }
      });
  });

  $("body").on("click", ".remove-filter", function () {
    const filterType = $(this).data("key");
    const attId = $(this).data("attid");
    console.log(filterType, attId);
    if (filterType == "times") {
      $("#time-slider").slider("values", [0, 1440]);
      updateTimeInputs(0, 1440);
      //   gọi lại ajax
      const priceValues = $("#price-slider").slider("values");
      fetchFilteredResults(0, 1440, priceValues[0], priceValues[1]);
    } else if (filterType == "prices") {
      $("#price-slider").slider("values", [0, 2000000]);
      updatePriceInputs(0, 2000000);
      //   gọi lại ajax
      const timeValues = $("#time-slider").slider("values");
      fetchFilteredResults(timeValues[0], timeValues[1], 0, 2000000);
    } else {
      $(".company-item input[type='checkbox']").each(function () {
        if ($(this).attr("id") == attId) {
          $(this).prop("checked", false);
          $(this).change();
        }
      });
    }
    $(this).parent().remove();
  });

  // load more
  $("body").on("click", ".load-more", function () {
    $(this).prepend(loadingBtn);
    $(this).attr("disabled", true);
    $(this).addClass("loading");
    const filterUrl = new URL(window.location.href);
    const page = $(this).data("page");
    filterUrl.searchParams.set("page", page);
    const timeValues = $("#time-slider").slider("values");
    const priceValues = $("#price-slider").slider("values");
    $(this).remove();
    fetchFilteredResults(
      timeValues[0],
      timeValues[1],
      priceValues[0],
      priceValues[1],
      "append",
      filterUrl
    );
  });

  // Add event listener for company checkbox changes
  $('.company-item input[type="checkbox"]').on("change", function () {
    // check is checked
    const isChecked = $(this).is(":checked");
    // check is parrent
    const isParent = $(this).closest(".company-item").hasClass("parent");
    // checked all child if is parent
    if (isParent) {
      $(this)
        .closest(".filter-item")
        .find(".list-child input[type='checkbox']")
        .prop("checked", isChecked);
    }
    // Delay AJAX call by 400ms when checkboxes are changed
    clearTimeout(window.checkboxTimeout);
    window.checkboxTimeout = setTimeout(function () {
      // Get current slider values
      const timeValues = $("#time-slider").slider("values");
      const priceValues = $("#price-slider").slider("values");
      // Call AJAX function with current values
      fetchFilteredResults(
        timeValues[0],
        timeValues[1],
        priceValues[0],
        priceValues[1]
      );
    }, 400);
  });

  // Initialize proper arrow directions based on initial state
  // If sections are initially visible, arrows should point up
  $(".sidebar-section")
    .find(".section-content")
    .each(function () {
      if ($(this).is(":visible")) {
        $(this)
          .closest(".sidebar-section")
          .find(".button-toggle svg")
          .addClass("rotate-180");
      }
    });

  // slider
  $(".product-detail__content-item.product-images").each(function () {
    const mySwiper = $(this).find(".mySwiper")[0];
    const mySwiper2 = $(this).find(".mySwiper2")[0];
    var swiper = new Swiper(mySwiper, {
      spaceBetween: 10,
      slidesPerView: 5,
      freeMode: true,
      watchSlidesProgress: true,
    });
    var swiper2 = new Swiper(mySwiper2, {
      spaceBetween: 10,
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      thumbs: {
        swiper: swiper,
      },
    });
  });

  $("body").on("click", ".product-detail__tab-item", function () {
    const tab = $(this).data("tab");
    $(this)
      .closest(".product-detail")
      .find(".product-detail__tab-item")
      .removeClass("active");
    $(this).addClass("active");
    $(this)
      .closest(".product-detail")
      .find(".product-detail__content-item")
      .removeClass("active");
    $(this)
      .closest(".product-detail")
      .find(".product-detail__content-item." + tab)
      .addClass("active");
  });

  $("body").on("click", ".open-information", function () {
    $(this).toggleClass("active");
    $(this)
      .closest(".border-box")
      .find(".product-detail")
      .toggleClass("active");
  });
  $("body").on("click", ".close-information", function () {
    $(this)
      .closest(".border-box")
      .find(".open-information")
      .removeClass("active");
    $(this)
      .closest(".border-box")
      .find(".product-detail")
      .removeClass("active");
  });
  $("body").on("click", ".open-child", function () {
    $(this).toggleClass("active");
    $(this).closest(".filter-item").find(".list-child").toggleClass("active");
  });
  $("body").on("click", ".open-popup", function () {
    const productId = $(this).data("product-id");
    const productName = $(this).data("product-name");
    const productPrice = $(this).data("product-price");
    const productPriceFormat = $(this).data("product-price-format");
    const brandName = $(this).data("brand-name");
    $("#popup-booking").find(".product-id").val(productId);
    $("#popup-booking").find(".product-name").text(productName);
    $("#popup-booking").find(".product-price").text(productPriceFormat);
    $("#popup-booking").find(".brand-name").text(brandName);
    $("#popup-booking").find(".total-price").text(productPriceFormat);
    $("#popup-booking").find("input.product-price").val(productPrice);
    $("#popup-booking").addClass("active");
    $("#popup-booking").find(".from").text($("#departure-display").val());
    $("#popup-booking").find(".to").text($("#destination-display").val());
    $("#popup-booking").find(".time").text($("#departure-date").val());
    const checkIsReturnBooking = $("#popup-booking").find("button[type='submit']").data(
      "isShowReturn"
    );
    if (checkIsReturnBooking) {
      $("#popup-booking").find(".time").text($("#return-date").val());
      $("#popup-booking").find(".from").text($("#destination-display").val());
      $("#popup-booking").find(".to").text($("#departure-display").val());
    }
    let locations = $(this).closest(".border-box").find(".info-location").data("locations");
    if (locations && locations.length > 0) {
      let locationPickupHtml = "";
      const locationPickup = locations.filter(item => item.type === 'pickup');
      const locationPickupFirst = locations.filter(item => item.type === 'pickup').slice(0, 1)[0];
      $.each(locationPickup, function (index, value) {
        locationPickupHtml += `<option value="${value.id}" data-transit="${value?.transit}">${value.time} - ${value.name}</option>`;
      });
      $("#popup-booking").find("select[name='pickup']").html(locationPickupHtml);
      $("#popup-booking").find("select[name='pickup']").val(locationPickupFirst?.id || 0).change();
      let locationDropOffHtml = "";
      const locationDropOff = locations.filter(item => item.type === 'dropoff');
      const locationDropOffLast = locations.filter(item => item.type === 'dropoff').slice(-1)[0];
      $.each(locationDropOff, function (index, value) {
        locationDropOffHtml += `<option value="${value.id}" data-transit="${value?.transit}">${value.time} - ${value.name}</option>
        </div>`;
      });
      $("#popup-booking").find("select[name='dropoff']").html(locationDropOffHtml);
      $("#popup-booking").find("select[name='dropoff']").val(locationDropOffLast?.id || 0).change();
    }
  });
  $("body").on("change", ".select-location", function () {
    const selectedOption = $(this).find("option:selected");
    const transitTime = selectedOption.data("transit");
    if (transitTime) {
      $(this).closest(".location-wrapper").find(".location-detail").removeClass("hidden");
    } else {
      $(this).closest(".location-wrapper").find(".location-detail").addClass("hidden");
    }
  });
  $("body").on("click", ".close-popup", function () {
    $("#popup-booking").find(".product-id").val("");
    $("#popup-booking").removeClass("active");
  });

  // viêt js cong tru so luong cho class .btn-qty, nếu có class btn-minus thì trừ, nếu có class btn-plus thì cộng
  $("body").on("click", ".btn-qty", function () {
    const qty = $(this).parent().find("input.quantity");
    let value = parseInt(qty.val());
    if ($(this).hasClass("btn-minus")) {
      if (value > 1) {
        value--;
      }
    } else if ($(this).hasClass("btn-plus")) {
      value++;
    }
    qty.val(value);
    const price = $("#popup-booking").find("input.product-price").val();
    $("#popup-booking")
      .find(".total-price")
      .text(formatCurrency(value * price));
  });

  $("body").on("submit", ".form-booking", function (e) {
    e.preventDefault();
    const button = $(this).find("button[type='submit']");
    const checkIsReturnBooking = button.data(
      "isShowReturn"
    );
    const pickUpId = $(this).find("select[name='pickup']").val();
    const dropOffId = $(this).find("select[name='dropoff']").val();
    const pickUpAddress = $(this).find('textarea[name="pickup_detail"]').val();
    const dropOffAddress = $(this).find('textarea[name="dropoff_detail"]').val();
    const formData = {
      departure: checkIsReturnBooking ? $("#destination").val() : $("#departure").val(),
      destination: checkIsReturnBooking ? $("#departure").val() : $("#destination").val(),
      departureDate: checkIsReturnBooking ? $("#return-date").val() : $("#departure-date").val(),
      returnDate: $("#return-date").val(),
      productId: $(this).find(".product-id").val(),
      quantity: $(this).find(".quantity").val(),
      name: $(this).find("input[name='name']").val(),
      phone: $(this).find("input[name='phone']").val(),
      cccd: $(this).find("input[name='cccd']").val(),
      pickUpId: pickUpId,
      dropOffId: dropOffId,
      pickUpAddress: pickUpAddress,
      dropOffAddress: dropOffAddress,
    };
    $.ajax({
      url: "/ajax/place-order",
      type: "POST",
      dataType: "json",
      data: formData,
      beforeSend: function () {
        button.prepend(loadingBtn);
        button.attr("disabled", true);
        button.addClass("loading");
        button.closest("form").find(".error").text("");
      },
      success: function (response) {
        if (response.error) {
          if (response.type == "validation") {
            button.closest("form").find(".error").text(response.message);
          }
        } else {
          alert_show("success", response.message);
          $("#popup-booking").removeClass("active");
          $("#popup-booking").find('textarea[name="pickup_detail"]').val('');
          $("#popup-booking").find('textarea[name="dropoff_detail"]').val('');
          // hiển thị dữ liệu chiều về nếu có chọn ngày về
          if (returnDatePicker.selectedDates[0] && !checkIsReturnBooking) {
            button.data("isShowReturn", 1);
            // load ajax chiều về
            clearTimeout(window.checkboxTimeout);
            window.checkboxTimeout = setTimeout(function () {
              // Get current slider values
              const timeValues = $("#time-slider").slider("values");
              const priceValues = $("#price-slider").slider("values");
              const filterUrl = new URL(window.location.href);
              filterUrl.searchParams.set("isShowReturn", 1);
              // Call AJAX function with current values
              fetchFilteredResults(
                timeValues[0],
                timeValues[1],
                priceValues[0],
                priceValues[1],
                'replace',
                filterUrl
              );
            }, 400);
          }
        }
        button.find("#loader-1").remove();
        button.attr("disabled", false);
        button.removeClass("loading");
      },
      error: function (e) {
        button.find("#loader-1").remove();
        button.attr("disabled", false);
        button.removeClass("loading");
        alert_show("error", e.message || "Có lỗi xảy ra, vui lòng thử lại sau");
      },
    });
  });
  $("body").on("click", ".open-change-time", function (e) {
    e.preventDefault();
    $(this).closest(".change-time-box").hide();
    $(".banner.search-page").show();
  });
  $("body").on("click", ".close-change-time", function (e) {
    e.preventDefault();
    $(".change-time-box").show();
    $(this).closest(".banner.search-page").hide();
  });
  $("body").on("click", ".open-filter", function (e) {
    e.preventDefault();
    $(".content-left.mobile").addClass("active");
  });
  $("body").on("click", ".close-sidebar", function (e) {
    e.preventDefault();
    $(".content-left.mobile").removeClass("active");
  });




});



