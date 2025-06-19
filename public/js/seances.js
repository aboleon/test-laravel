const timetable = {
  accessor: $('#add-timetable'),
  template: $('#template-timetable'),
  container: $('#timetables'),
  subelement: '.working_periods',
  lastAdded: function () {
    return timetable.container.find('.working_periods').last();
  },
  countStages: function () {
    return this.container.find(this.subelement).length;
  },
  add: function () {
    this.accessor.click(function () {
      let checked = [],
        counter = timetable.countStages();

      if (timetable.controlRequired()) {
        timetable.container.append(timetable.template.html());
        //timetable.renameAttributes();
        setDatepicker();
        timetable.removePeriod();
        resetIteration(timetable.container);
      }
    });
  },
  controlRequired: function () {
    return true;
  },
  renameAttributes: function () {
    attributeUpdater(timetable.lastAdded(), 'working_time');
  },
  cloneManipulation: function (cloned) {
      cloned.find('.flatpickr-input').removeClass('flatpickr-input');
  },
  removePeriod: function () {
    removable();
  },
  init: function () {
    this.add();
    this.removePeriod();
  },
};
timetable.init();

