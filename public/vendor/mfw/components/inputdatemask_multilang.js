document.querySelectorAll('.inputdatemask-multilang').forEach(function(input) {
  input.addEventListener('input', function(e) {
    var format = this.getAttribute('data-format');
    var value = this.value.replace(/\D/g, '').substr(0, 8);
    var day = '';
    var month = '';
    var year = '';

    if (value.length > 0) {
      var first = value.substr(0, 2);
      var second = value.substr(2, 2);
      var third = value.substr(4);


      switch (format) {
        case 'D/M/Y':
          day = first;
          month = second;
          year = third;
          break;
        case 'M/D/Y':
          month = first;
          day = second;
          year = third;
          break;
      }


      day = parseInt(day) > 31 ? '31' : day;
      month = parseInt(month) > 12 ? '12' : month;


      if (year.length === 4) {
        let currentYear = new Date().getFullYear();
        let maxYear = currentYear + 100;
        let minYear = currentYear - 100;
        year = parseInt(year) > maxYear ? maxYear.toString() : year;
        year = parseInt(year) < minYear ? minYear.toString() : year;
      }
    }

    var formattedValue = '';
    switch (format) {
      case 'D/M/Y':
        if (day) formattedValue += day + '/';
        if (month) formattedValue += month + '/';
        if (year) formattedValue += year;
        break;
      case 'M/D/Y':
        if (month) formattedValue += month + '/';
        if (day) formattedValue += day + '/';
        if (year) formattedValue += year;
        break;
    }

    this.value = formattedValue;
  });
});
