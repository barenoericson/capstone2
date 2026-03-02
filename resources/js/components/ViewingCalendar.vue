<template>
  <div class="viewing-calendar">
    <!-- Month Navigation -->
    <div class="cal-header">
      <button class="cal-nav" @click="prevMonth">&larr;</button>
      <span class="cal-title">{{ monthName }} {{ currentYear }}</span>
      <button class="cal-nav" @click="nextMonth">&rarr;</button>
    </div>

    <!-- Day Names -->
    <div class="cal-weekdays">
      <span v-for="d in dayNames" :key="d" class="cal-weekday">{{ d }}</span>
    </div>

    <!-- Day Grid -->
    <div class="cal-grid">
      <div
        v-for="(day, idx) in calendarDays"
        :key="idx"
        class="cal-cell"
        :class="cellClass(day)"
        @click="selectDay(day)"
      >
        <span v-if="day.date">{{ day.dayNum }}</span>
      </div>
    </div>

    <!-- Legend -->
    <div class="cal-legend">
      <span class="legend-item"><span class="dot dot-green"></span> Available</span>
      <span class="legend-item"><span class="dot dot-red"></span> Booked</span>
      <span class="legend-item"><span class="dot dot-gray"></span> Unavailable</span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ViewingCalendar',

  props: {
    blockedDates: { type: Array, default: () => [] },
    bookedDates: { type: Array, default: () => [] },
    selectedDate: { type: String, default: '' },
  },

  emits: ['update:selectedDate'],

  data() {
    const now = new Date();
    return {
      currentMonth: now.getMonth(),
      currentYear: now.getFullYear(),
      dayNames: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
    };
  },

  computed: {
    monthName() {
      return new Date(this.currentYear, this.currentMonth).toLocaleString('en-PH', { month: 'long' });
    },

    todayStr() {
      const d = new Date();
      return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
    },

    calendarDays() {
      const year = this.currentYear;
      const month = this.currentMonth;
      const firstDay = new Date(year, month, 1).getDay();
      const daysInMonth = new Date(year, month + 1, 0).getDate();

      const days = [];

      // Empty cells for days before the 1st
      for (let i = 0; i < firstDay; i++) {
        days.push({ date: null, dayNum: null });
      }

      // Actual days
      for (let d = 1; d <= daysInMonth; d++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        days.push({ date: dateStr, dayNum: d });
      }

      return days;
    },
  },

  methods: {
    cellClass(day) {
      if (!day.date) return 'cal-empty';

      const isPast = day.date < this.todayStr;
      const isBlocked = this.blockedDates.includes(day.date);
      const isBooked = this.bookedDates.includes(day.date);
      const isSelected = day.date === this.selectedDate;

      if (isSelected) return 'cal-selected';
      if (isPast || isBlocked) return 'cal-blocked';
      if (isBooked) return 'cal-booked';
      return 'cal-available';
    },

    selectDay(day) {
      if (!day.date) return;
      if (day.date < this.todayStr) return;
      if (this.blockedDates.includes(day.date)) return;

      this.$emit('update:selectedDate', day.date);
    },

    prevMonth() {
      if (this.currentMonth === 0) {
        this.currentMonth = 11;
        this.currentYear--;
      } else {
        this.currentMonth--;
      }
    },

    nextMonth() {
      if (this.currentMonth === 11) {
        this.currentMonth = 0;
        this.currentYear++;
      } else {
        this.currentMonth++;
      }
    },
  },
};
</script>

<style scoped>
.viewing-calendar {
  width: 100%;
  max-width: 360px;
  margin: 0 auto;
}

.cal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}

.cal-title {
  font-size: 16px;
  font-weight: 700;
  color: #100c08;
}

.cal-nav {
  background: none;
  border: 1px solid #ddd;
  border-radius: 6px;
  width: 32px;
  height: 32px;
  font-size: 16px;
  cursor: pointer;
  color: #100c08;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s;
}
.cal-nav:hover {
  background: #FFD700;
  border-color: #FFD700;
  color: #100c08;
}

.cal-weekdays {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 2px;
  margin-bottom: 4px;
}
.cal-weekday {
  text-align: center;
  font-size: 11px;
  font-weight: 600;
  color: #9ca3af;
  padding: 4px 0;
  text-transform: uppercase;
}

.cal-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 3px;
}

.cal-cell {
  aspect-ratio: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.15s;
  max-width: 42px;
  max-height: 42px;
  margin: 0 auto;
  width: 100%;
}

.cal-empty {
  cursor: default;
}

.cal-available {
  background: #dcfce7;
  color: #166534;
}
.cal-available:hover {
  background: #16a34a;
  color: #fff;
}

.cal-booked {
  background: #fee2e2;
  color: #991b1b;
  cursor: not-allowed;
}

.cal-blocked {
  background: #f3f4f6;
  color: #9ca3af;
  cursor: not-allowed;
}

.cal-selected {
  background: #FFD700;
  color: #100c08;
  font-weight: 700;
  box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.3);
}

/* Legend */
.cal-legend {
  display: flex;
  gap: 16px;
  justify-content: center;
  margin-top: 14px;
  flex-wrap: wrap;
}
.legend-item {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  color: #6b7280;
}
.dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  display: inline-block;
}
.dot-green { background: #16a34a; }
.dot-red { background: #dc2626; }
.dot-gray { background: #9ca3af; }
</style>
