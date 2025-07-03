<input type="text" id="datetime" class="form-control">

<!-- Flatpickr CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>

<script>
  flatpickr("#datetime", {
    enableTime: true,
    dateFormat: "d/m/Y H:i",
    locale: "th",
    time_24hr: true
  });
</script>
