jQuery(function (jQuery) {
    jQuery('#' + orddd_lite_access.orddd_lite_field_name ).datepicker({
      onClose: removeAria
    });
  
    // Add aria-describedby to the button referring to the label
    jQuery('#' + orddd_lite_access.orddd_lite_field_name ).attr('aria-describedby', 'Delivery Date');
  
    dayTripper();
  
  });
  
  
  function dayTripper() {
    jQuery('#' + orddd_lite_access.orddd_lite_field_name ).focus(function () {
      setTimeout(function () {
        var today = jQuery('.ui-datepicker-today a')[0];
        
        if (!today || undefined !== jQuery('.ui-state-active')[0] ) {
          today = jQuery('.ui-state-active')[0] ||
                  jQuery('a.ui-state-default')[0];
        }
  
        // Hide the entire page (except the date picker)
        // from screen readers to prevent document navigation
        // (by headings, etc.) while the popup is open
        jQuery("#e_deliverydate_field").attr('aria-hidden','true');
        jQuery("#skipnav").attr('aria-hidden','true');
  
        // Hide the "today" button because it doesn't do what
        // you think it supposed to do
        jQuery(".ui-datepicker-current").hide();
  
        today.focus();
        datePickHandler();
        jQuery(document).on('click', '#ui-datepicker-div .ui-datepicker-close', function () {
          closeCalendar();
        });
      }, 0);
    });
  }
  
  function datePickHandler() {
    var activeDate;
    var container = document.getElementById('ui-datepicker-div');
    var input = document.getElementById('e_deliverydate');
  
    if (!container || !input) {
      return;
    }
  
   // jQuery(container).find('table').first().attr('role', 'grid');
  
    container.setAttribute('role', 'application');
    container.setAttribute('aria-label', 'Delivery date Datepicker');
  
      // the top controls:
    var prev = jQuery('.ui-datepicker-prev', container)[0],
        next = jQuery('.ui-datepicker-next', container)[0];
  
  
  // This is the line that needs to be fixed for use on pages with base URL set in head
    next.href = 'javascript:void(0)';
    prev.href = 'javascript:void(0)';
  
    next.setAttribute('role', 'button');
    next.removeAttribute('title');
    prev.setAttribute('role', 'button');
    prev.removeAttribute('title');
  
    appendOffscreenMonthText(next);
    appendOffscreenMonthText(prev);
  
    // delegation won't work here for whatever reason, so we are
    // forced to attach individual click listeners to the prev /
    // next month buttons each time they are added to the DOM
    jQuery(next).on('click', handleNextClicks);
    jQuery(prev).on('click', handlePrevClicks);
  
    monthDayYearText();
  
    jQuery(container).on('keydown', function calendarKeyboardListener(keyVent) {
      var which = keyVent.which;
      var target = keyVent.target;
      var dateCurrent = getCurrentDate(container);
  
      if (!dateCurrent) {
        dateCurrent = jQuery('a.ui-state-default')[0];
        setHighlightState(dateCurrent, container);
      }
  
      if (27 === which) {
        keyVent.stopPropagation();
        return closeCalendar();
      } else if (which === 9 && keyVent.shiftKey) { // SHIFT + TAB
        keyVent.preventDefault();
        if (jQuery(target).hasClass('ui-datepicker-close')) { // close button
          jQuery('.ui-datepicker-prev')[0].focus();
        } else if (jQuery(target).hasClass('ui-state-default')) { // a date link
          jQuery('.ui-datepicker-close')[0].focus();
        } else if (jQuery(target).hasClass('ui-datepicker-prev')) { // the prev link
          jQuery('.ui-datepicker-next')[0].focus();
        } else if (jQuery(target).hasClass('ui-datepicker-next')) { // the next link
          activeDate = jQuery('.ui-state-highlight') ||
                      jQuery('.ui-state-active')[0];
          if (activeDate) {
            activeDate.focus();
          }
        }
      } else if (which === 9) { // TAB
        keyVent.preventDefault();
        if (jQuery(target).hasClass('ui-datepicker-close')) { // close button
          activeDate = jQuery('.ui-state-highlight') ||
                      jQuery('.ui-state-active')[0];
          if (activeDate) {
            activeDate.focus();
          }
        } else if (jQuery(target).hasClass('ui-state-default')) {
          jQuery('.ui-datepicker-next')[0].focus();
        } else if (jQuery(target).hasClass('ui-datepicker-next')) {
          jQuery('.ui-datepicker-prev')[0].focus();
        } else if (jQuery(target).hasClass('ui-datepicker-prev')) {
          jQuery('.ui-datepicker-close')[0].focus();
        }
      } else if (which === 37) { // LEFT arrow key
        // if we're on a date link...
        if (!jQuery(target).hasClass('ui-datepicker-close') && jQuery(target).hasClass('ui-state-default')) {
          keyVent.preventDefault();
          previousDay(target);
        }
      } else if (which === 39) { // RIGHT arrow key
        // if we're on a date link...
        if (!jQuery(target).hasClass('ui-datepicker-close') && jQuery(target).hasClass('ui-state-default')) {
          keyVent.preventDefault();
          nextDay(target);
        }
      } else if (which === 38) { // UP arrow key
        if (!jQuery(target).hasClass('ui-datepicker-close') && jQuery(target).hasClass('ui-state-default')) {
          keyVent.preventDefault();
          upHandler(target, container, prev);
        }
      } else if (which === 40) { // DOWN arrow key
        if (!jQuery(target).hasClass('ui-datepicker-close') && jQuery(target).hasClass('ui-state-default')) {
          keyVent.preventDefault();
          downHandler(target, container, next);
        }
      } else if (which === 13) { // ENTER
        if (jQuery(target).hasClass('ui-state-default')) {
          setTimeout(function () {
            closeCalendar();
          }, 100);
        } else if (jQuery(target).hasClass('ui-datepicker-prev')) {
          handlePrevClicks();
        } else if (jQuery(target).hasClass('ui-datepicker-next')) {
          handleNextClicks();
        }
      } else if (32 === which) {
        if (jQuery(target).hasClass('ui-datepicker-prev') || jQuery(target).hasClass('ui-datepicker-next')) {
          target.click();
        }
      } else if (33 === which) { // PAGE UP
        moveOneMonth(target, 'prev');
      } else if (34 === which) { // PAGE DOWN
        moveOneMonth(target, 'next');
      } else if (36 === which) { // HOME
        var firstOfMonth = jQuery(target).closest('tbody').find('.ui-state-default')[0];
        if (firstOfMonth) {
          firstOfMonth.focus();
          setHighlightState(firstOfMonth, jQuery('#ui-datepicker-div')[0]);
        }
      } else if (35 === which) { // END
        var jQuerydaysOfMonth = jQuery(target).closest('tbody').find('.ui-state-default');
        var lastDay = jQuerydaysOfMonth[jQuerydaysOfMonth.length - 1];
        if (lastDay) {
          lastDay.focus();
          setHighlightState(lastDay, jQuery('#ui-datepicker-div')[0]);
        }
      }
      jQuery(".ui-datepicker-current").hide();
    });
  }
  
  function closeCalendar() {
    var container = jQuery('#ui-datepicker-div');
    jQuery(container).off('keydown');
    var input = jQuery('#' + orddd_lite_access.orddd_lite_field_name )[0];
    jQuery(input).datepicker('hide');
  
   // input.focus();
  }
  
  function removeAria() {
    // make the rest of the page accessible again:
    jQuery("#e_deliverydate_field").removeAttr('aria-hidden');
    jQuery("#skipnav").removeAttr('aria-hidden');
  }
  
  ///////////////////////////////
  //////////////////////////// //
  ///////////////////////// // //
  // UTILITY-LIKE THINGS // // //
  ///////////////////////// // //
  //////////////////////////// //
  ///////////////////////////////
  function isOdd(num) {
    return num % 2;
  }
  
  function moveOneMonth(currentDate, dir) {
    var button = (dir === 'next')
                ? jQuery('.ui-datepicker-next')[0]
                : jQuery('.ui-datepicker-prev')[0];
  
    if (!button) {
      return;
    }
  
    var ENABLED_SELECTOR = '#ui-datepicker-div tbody td:not(.ui-state-disabled)';
    var jQuerycurrentCells = jQuery(ENABLED_SELECTOR);
    var currentIdx = jQuery.inArray(currentDate.parentNode, jQuerycurrentCells);
  
    button.click();
    setTimeout(function () {
      updateHeaderElements();
  
      var jQuerynewCells = jQuery(ENABLED_SELECTOR);
      var newTd = jQuerynewCells[currentIdx];
      var newAnchor = newTd && jQuery(newTd).find('a')[0];
  
      while (!newAnchor) {
        currentIdx--;
        newTd = jQuerynewCells[currentIdx];
        newAnchor = newTd && jQuery(newTd).find('a')[0];
      }
  
      setHighlightState(newAnchor, jQuery('#ui-datepicker-div')[0]);
      newAnchor.focus();
  
    }, 0);
  
  }
  
  function handleNextClicks() {
    setTimeout(function () {
      updateHeaderElements();
      prepHighlightState();
      jQuery('.ui-datepicker-next').focus();
      jQuery(".ui-datepicker-current").hide();
    }, 0);
  }
  
  function handlePrevClicks() {
    setTimeout(function () {
      updateHeaderElements();
      prepHighlightState();
      jQuery('.ui-datepicker-prev').focus();
      jQuery(".ui-datepicker-current").hide();
    }, 0);
  }
  
  function previousDay(dateLink) {
    var container = document.getElementById('ui-datepicker-div');
    if (!dateLink) {
      return;
    }
    var td = jQuery(dateLink).closest('td');
    if (!td) {
      return;
    }
  
    var prevTd = jQuery(td).prev(),
        prevDateLink = jQuery('a.ui-state-default', prevTd)[0];
  
    if (prevTd && prevDateLink) {
      setHighlightState(prevDateLink, container);
      prevDateLink.focus();
    } else {
      handlePrevious(dateLink);
    }
  }
  
  
  function handlePrevious(target) {
    var container = document.getElementById('ui-datepicker-div');
    if (!target) {
      return;
    }
    var currentRow = jQuery(target).closest('tr');
    if (!currentRow) {
      return;
    }
    var previousRow = jQuery(currentRow).prev();
  
    if (!previousRow || previousRow.length === 0) {
      // there is not previous row, so we go to previous month...
      previousMonth();
    } else {
      var prevRowDates = jQuery('td a.ui-state-default', previousRow);
      var prevRowDate = prevRowDates[prevRowDates.length - 1];
  
      if (prevRowDate) {
        setTimeout(function () {
          setHighlightState(prevRowDate, container);
          prevRowDate.focus();
        }, 0);
      }
    }
  }
  
  function previousMonth() {
    var prevLink = jQuery('.ui-datepicker-prev')[0];
    var container = document.getElementById('ui-datepicker-div');
    prevLink.click();
    // focus last day of new month
    setTimeout(function () {
      var trs = jQuery('tr', container),
          lastRowTdLinks = jQuery('td a.ui-state-default', trs[trs.length - 1]),
          lastDate = lastRowTdLinks[lastRowTdLinks.length - 1];
  
      // updating the cached header elements
      updateHeaderElements();
  
      setHighlightState(lastDate, container);
      lastDate.focus();
  
    }, 0);
  }
  
  ///////////////// NEXT /////////////////
  /**
   * Handles right arrow key navigation
   * @param  {HTMLElement} dateLink The target of the keyboard event
   */
  function nextDay(dateLink) {
    var container = document.getElementById('ui-datepicker-div');
    if (!dateLink) {
      return;
    }
    var td = jQuery(dateLink).closest('td');
    if (!td) {
      return;
    }
    var nextTd = jQuery(td).next(),
        nextDateLink = jQuery('a.ui-state-default', nextTd)[0];
  
    if (nextTd && nextDateLink) {
      setHighlightState(nextDateLink, container);
      nextDateLink.focus(); // the next day (same row)
    } else {
      handleNext(dateLink);
    }
  }
  
  function handleNext(target) {
    var container = document.getElementById('ui-datepicker-div');
    if (!target) {
      return;
    }
    var currentRow = jQuery(target).closest('tr'),
        nextRow = jQuery(currentRow).next();
  
    if (!nextRow || nextRow.length === 0) {
      nextMonth();
    } else {
      var nextRowFirstDate = jQuery('a.ui-state-default', nextRow)[0];
      if (nextRowFirstDate) {
        setHighlightState(nextRowFirstDate, container);
        nextRowFirstDate.focus();
      }
    }
  }
  
  function nextMonth() {
    nextMon = jQuery('.ui-datepicker-next')[0];
    var container = document.getElementById('ui-datepicker-div');
    nextMon.click();
    // focus the first day of the new month
    setTimeout(function () {
      // updating the cached header elements
      updateHeaderElements();
  
      var firstDate = jQuery('a.ui-state-default', container)[0];
      setHighlightState(firstDate, container);
      firstDate.focus();
    }, 0);
  }
  
  /////////// UP ///////////
  /**
   * Handle the up arrow navigation through dates
   * @param  {HTMLElement} target   The target of the keyboard event (day)
   * @param  {HTMLElement} cont     The calendar container
   * @param  {HTMLElement} prevLink Link to navigate to previous month
   */
  function upHandler(target, cont, prevLink) {
    prevLink = jQuery('.ui-datepicker-prev')[0];
    var rowContext = jQuery(target).closest('tr');
    if (!rowContext) {
      return;
    }
    var rowTds = jQuery('td', rowContext),
        rowLinks = jQuery('a.ui-state-default', rowContext),
        targetIndex = jQuery.inArray(target, rowLinks),
        prevRow = jQuery(rowContext).prev(),
        prevRowTds = jQuery('td', prevRow),
        parallel = prevRowTds[targetIndex],
        linkCheck = jQuery('a.ui-state-default', parallel)[0];
  
    if (prevRow && parallel && linkCheck) {
      // there is a previous row, a td at the same index
      // of the target AND theres a link in that td
      setHighlightState(linkCheck, cont);
      linkCheck.focus();
    } else {
      // we're either on the first row of a month, or we're on the
      // second and there is not a date link directly above the target
      prevLink.click();
      setTimeout(function () {
        // updating the cached header elements
        updateHeaderElements();
        var newRows = jQuery('tr', cont),
            lastRow = newRows[newRows.length - 1],
            lastRowTds = jQuery('td', lastRow),
            tdParallelIndex = jQuery.inArray(target.parentNode, rowTds),
            newParallel = lastRowTds[tdParallelIndex],
            newCheck = jQuery('a.ui-state-default', newParallel)[0];
  
        if (lastRow && newParallel && newCheck) {
          setHighlightState(newCheck, cont);
          newCheck.focus();
        } else {
          // theres no date link on the last week (row) of the new month
          // meaning its an empty cell, so we'll try the 2nd to last week
          var secondLastRow = newRows[newRows.length - 2],
              secondTds = jQuery('td', secondLastRow),
              targetTd = secondTds[tdParallelIndex],
              linkCheck = jQuery('a.ui-state-default', targetTd)[0];
  
          if (linkCheck) {
            setHighlightState(linkCheck, cont);
            linkCheck.focus();
          }
  
        }
      }, 0);
    }
  }
  
  //////////////// DOWN ////////////////
  /**
   * Handles down arrow navigation through dates in calendar
   * @param  {HTMLElement} target   The target of the keyboard event (day)
   * @param  {HTMLElement} cont     The calendar container
   * @param  {HTMLElement} nextLink Link to navigate to next month
   */
  function downHandler(target, cont, nextLink) {
    nextLink = jQuery('.ui-datepicker-next')[0];
    var targetRow = jQuery(target).closest('tr');
    if (!targetRow) {
      return;
    }
    var targetCells = jQuery('td', targetRow),
        cellIndex = jQuery.inArray(target.parentNode, targetCells), // the td (parent of target) index
        nextRow = jQuery(targetRow).next(),
        nextRowCells = jQuery('td', nextRow),
        nextWeekTd = nextRowCells[cellIndex],
        nextWeekCheck = jQuery('a.ui-state-default', nextWeekTd)[0];
  
    if (nextRow && nextWeekTd && nextWeekCheck) {
      // theres a next row, a TD at the same index of `target`,
      // and theres an anchor within that td
      setHighlightState(nextWeekCheck, cont);
      nextWeekCheck.focus();
    } else {
      nextLink.click();
  
      setTimeout(function () {
        // updating the cached header elements
        updateHeaderElements();
  
        var nextMonthTrs = jQuery('tbody tr', cont),
            firstTds = jQuery('td', nextMonthTrs[0]),
            firstParallel = firstTds[cellIndex],
            firstCheck = jQuery('a.ui-state-default', firstParallel)[0];
  
        if (firstParallel && firstCheck) {
          setHighlightState(firstCheck, cont);
          firstCheck.focus();
        } else {
          // lets try the second row b/c we didnt find a
          // date link in the first row at the target's index
          var secondRow = nextMonthTrs[1],
              secondTds = jQuery('td', secondRow),
              secondRowTd = secondTds[cellIndex],
              secondCheck = jQuery('a.ui-state-default', secondRowTd)[0];
  
          if (secondRow && secondCheck) {
            setHighlightState(secondCheck, cont);
            secondCheck.focus();
          }
        }
      }, 0);
    }
  }
  
  
  function onCalendarHide() {
    closeCalendar();
  }
  
  // add an aria-label to the date link indicating the currently focused date
  // (formatted identically to the required format: mm/dd/yyyy)
  function monthDayYearText() {
    var cleanUps = jQuery('.amaze-date');
  
    jQuery(cleanUps).each(function (clean) {
    // each(cleanUps, function (clean) {
      clean.parentNode.removeChild(clean);
    });
  
    var datePickDiv = document.getElementById('ui-datepicker-div');
    // in case we find no datepick div
    if (!datePickDiv) {
      return;
    }
  
    var dates = jQuery('a.ui-state-default', datePickDiv);
    jQuery(dates).attr('role', 'button').on('keydown', function (e) {
      if (e.which === 32) {
        e.preventDefault();
        e.target.click();
        setTimeout(function () {
          closeCalendar();
        }, 100);
      }
    });
    jQuery(dates).each(function (index, date) {
      var currentRow = jQuery(date).closest('tr'),
          currentTds = jQuery('td', currentRow),
          currentIndex = jQuery.inArray(date.parentNode, currentTds),
          headThs = jQuery('thead tr th', datePickDiv),
          dayIndex = headThs[currentIndex],
          daySpan = jQuery('span', dayIndex)[0],
          monthName = jQuery('.ui-datepicker-month', datePickDiv)[0].innerHTML,
          year = jQuery('.ui-datepicker-year', datePickDiv)[0].innerHTML,
          number = date.innerHTML;
  
      if (!daySpan || !monthName || !number || !year) {
        return;
      }
  
      // AT Reads: {month} {date} {year} {day}
      // "December 18 2014 Thursday"
      var dateText = date.innerHTML + ' ' + monthName + ' ' + year + ' ' + daySpan.title;
      // AT Reads: {date(number)} {name of day} {name of month} {year(number)}
      // var dateText = date.innerHTML + ' ' + daySpan.title + ' ' + monthName + ' ' + year;
      // add an aria-label to the date link reading out the currently focused date
      date.setAttribute('aria-label', dateText);
    });
  }
  
  
  
  // update the cached header elements because we're in a new month or year
  function updateHeaderElements() {
    var context = document.getElementById('ui-datepicker-div');
    if (!context) {
      return;
    }
  
  //  jQuery(context).find('table').first().attr('role', 'grid');
  
    prev = jQuery('.ui-datepicker-prev', context)[0];
    next = jQuery('.ui-datepicker-next', context)[0];
  
    //make them click/focus - able
    next.href = 'javascript:void(0)';
    prev.href = 'javascript:void(0)';
  
    next.setAttribute('role', 'button');
    prev.setAttribute('role', 'button');
    appendOffscreenMonthText(next);
    appendOffscreenMonthText(prev);
  
    jQuery(next).on('click', handleNextClicks);
    jQuery(prev).on('click', handlePrevClicks);
  
    // add month day year text
    monthDayYearText();
  }
  
  
  function prepHighlightState() {
    var highlight;
    var cage = document.getElementById('ui-datepicker-div');
    highlight = jQuery('.ui-state-highlight', cage)[0] ||
                jQuery('.ui-state-default', cage)[0];
    if (highlight && cage) {
      setHighlightState(highlight, cage);
    }
  }
  
  // Set the highlighted class to date elements, when focus is received
  function setHighlightState(newHighlight, container) {
    var prevHighlight = getCurrentDate(container);
    // remove the highlight state from previously
    // highlighted date and add it to our newly active date
    jQuery(prevHighlight).removeClass('ui-state-highlight');
    jQuery(newHighlight).addClass('ui-state-highlight');
  }
  
  
  // grabs the current date based on the highlight class
  function getCurrentDate(container) {
    var currentDate = jQuery('.ui-state-highlight', container)[0];
    return currentDate;
  }
  
  /**
   * Appends logical next/prev month text to the buttons
   * - ex: Next Month, January 2015
   *       Previous Month, November 2014
   */
  function appendOffscreenMonthText(button) {
    var buttonText;
    var isNext = jQuery(button).hasClass('ui-datepicker-next');
    var months = jQuery.datepicker._defaults.monthNames;
    var numberOfMonths = orddd_lite_access.orddd_lite_number_of_months;

    var currentMonth = jQuery('.ui-datepicker-title .ui-datepicker-month').text();
    var monthIndex = jQuery.inArray(currentMonth, months);
    var currentYear = jQuery('.ui-datepicker-title .ui-datepicker-year').text();
   
    if ( '2' == numberOfMonths ) {
      if( isNext ) {
        currentMonth = jQuery('.ui-datepicker-group-last .ui-datepicker-title .ui-datepicker-month').text();
        currentYear = jQuery('.ui-datepicker-group-last .ui-datepicker-title .ui-datepicker-year').text();
      } else {
        currentMonth = jQuery('.ui-datepicker-group-first .ui-datepicker-title .ui-datepicker-month').text();
        currentYear = jQuery('.ui-datepicker-group-first .ui-datepicker-title .ui-datepicker-year').text();
      }
      monthIndex = jQuery.inArray(currentMonth, months);
    }
    var adjacentIndex = (isNext ) ? monthIndex + 1 : monthIndex - 1;

    if (isNext && currentMonth === months[11] ) {
      currentYear = parseInt(currentYear, 10) + 1;
      adjacentIndex = 0;
    } else if (!isNext && currentMonth === months[0]) {
      currentYear = parseInt(currentYear, 10) - 1;
      adjacentIndex = months.length - 1;
    }
    
    buttonText = (isNext) ? 'Next Month, ' + months[adjacentIndex] + ' ' + currentYear : 'Previous Month, ' + months[adjacentIndex] + ' ' + currentYear;
  
    jQuery(button).find('.ui-icon').html(buttonText);
  }
  
