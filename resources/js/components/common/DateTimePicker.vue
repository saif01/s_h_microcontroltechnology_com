<template>
    <v-menu v-model="menuOpen" :close-on-content-click="false" location="bottom start">
        <template v-slot:activator="{ props }">
            <v-text-field :model-value="displayValue" :label="label" :variant="variant"
                :prepend-inner-icon="prependIcon" :hint="hint" :persistent-hint="persistentHint" :readonly="readonly"
                :clearable="clearable" :required="required" :disabled="disabled" :error="error"
                :error-messages="errorMessages" v-bind="props" @click:clear="clearValue">
            </v-text-field>
        </template>
        <v-card :min-width="cardWidth" class="pa-1">
            <v-card-title class="text-caption mb-0 pa-1">
                <v-icon :icon="titleIcon" class="mr-1" size="small"></v-icon>
                {{ title || `Select ${label}` }}
            </v-card-title>
            <v-card-text class="pa-1">
                <div class="calendar-container mb-1">
                    <!-- Calendar Header -->
                    <div class="d-flex justify-space-between align-center mb-1 calendar-header">
                        <v-btn icon="mdi-chevron-left" variant="text" size="x-small" density="compact"
                            @click="previousMonth"></v-btn>
                        <div class="text-caption font-weight-medium">{{ currentMonthYear }}</div>
                        <v-btn icon="mdi-chevron-right" variant="text" size="x-small" density="compact"
                            @click="nextMonth"></v-btn>
                    </div>

                    <!-- Day Headers -->
                    <div class="calendar-weekdays d-flex">
                        <div class="calendar-weekday" v-for="day in weekDays" :key="day">{{ day }}</div>
                    </div>

                    <!-- Calendar Days -->
                    <div class="calendar-days">
                        <template v-for="(week, weekIndex) in calendarDays" :key="weekIndex">
                            <div class="calendar-week d-flex">
                                <button v-for="(day, dayIndex) in week" :key="dayIndex" class="calendar-day" :class="{
                                    'calendar-day-other-month': !day.currentMonth,
                                    'calendar-day-selected': day.selected,
                                    'calendar-day-today': day.isToday,
                                    'calendar-day-disabled': day.disabled
                                }" :disabled="day.disabled" @click="selectDate(day.date)">
                                    {{ day.day }}
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
                <v-divider class="my-1"></v-divider>
                <v-text-field v-model="timePicker" label="Time" type="time" variant="outlined" density="compact"
                    prepend-inner-icon="mdi-clock-outline" @update:model-value="updateDateTime" class="mt-1 time-field"
                    hide-details>
                </v-text-field>
            </v-card-text>
            <v-card-actions class="pa-1">
                <v-spacer></v-spacer>
                <v-btn variant="text" size="small" density="compact" @click="clearValue">Clear</v-btn>
                <v-btn color="primary" variant="text" size="small" density="compact"
                    @click="menuOpen = false">OK</v-btn>
            </v-card-actions>
        </v-card>
    </v-menu>
</template>

<script>
export default {
    name: 'DateTimePicker',
    props: {
        modelValue: {
            type: String,
            default: null
        },
        label: {
            type: String,
            default: 'Date & Time'
        },
        hint: {
            type: String,
            default: ''
        },
        persistentHint: {
            type: Boolean,
            default: false
        },
        variant: {
            type: String,
            default: 'outlined'
        },
        prependIcon: {
            type: String,
            default: 'mdi-calendar-clock'
        },
        title: {
            type: String,
            default: null
        },
        titleIcon: {
            type: String,
            default: 'mdi-calendar-clock'
        },
        clearable: {
            type: Boolean,
            default: true
        },
        readonly: {
            type: Boolean,
            default: true
        },
        required: {
            type: Boolean,
            default: false
        },
        disabled: {
            type: Boolean,
            default: false
        },
        error: {
            type: Boolean,
            default: false
        },
        errorMessages: {
            type: [String, Array],
            default: null
        },
        minDate: {
            type: String,
            default: null
        },
        maxDate: {
            type: String,
            default: null
        },
        cardWidth: {
            type: [String, Number],
            default: 280
        }
    },
    emits: ['update:modelValue'],
    data() {
        return {
            menuOpen: false,
            datePicker: null,
            timePicker: null,
            currentDate: new Date()
        };
    },
    computed: {
        displayValue() {
            if (!this.modelValue) return '';
            const date = new Date(this.modelValue);
            if (isNaN(date.getTime())) return '';
            return date.toLocaleString();
        },
        weekDays() {
            return ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        },
        currentMonthYear() {
            return this.currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        },
        calendarDays() {
            const year = this.currentDate.getFullYear();
            const month = this.currentDate.getMonth();

            // Get first day of month and last day
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startingDayOfWeek = firstDay.getDay();

            // Get previous month's days to fill the first week
            const prevMonth = new Date(year, month, 0);
            const daysInPrevMonth = prevMonth.getDate();

            const days = [];
            const weeks = [];
            let currentWeek = [];

            // Add previous month's days
            for (let i = startingDayOfWeek - 1; i >= 0; i--) {
                const day = daysInPrevMonth - i;
                const date = new Date(year, month - 1, day);
                currentWeek.push(this.createDayObject(date, false));
            }

            // Add current month's days
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                currentWeek.push(this.createDayObject(date, true));

                if (currentWeek.length === 7) {
                    weeks.push([...currentWeek]);
                    currentWeek = [];
                }
            }

            // Add next month's days to fill the last week
            let nextMonthDay = 1;
            while (currentWeek.length < 7) {
                const date = new Date(year, month + 1, nextMonthDay);
                currentWeek.push(this.createDayObject(date, false));
                nextMonthDay++;
            }
            if (currentWeek.length > 0) {
                weeks.push(currentWeek);
            }

            return weeks;
        }
    },
    watch: {
        modelValue: {
            immediate: true,
            handler(newValue) {
                if (newValue) {
                    const date = new Date(newValue);
                    if (!isNaN(date.getTime())) {
                        const dateStr = date.toISOString().split('T')[0];
                        this.datePicker = dateStr;
                        this.currentDate = new Date(date);
                        this.timePicker = String(date.getHours()).padStart(2, '0') + ':' + String(date.getMinutes()).padStart(2, '0');
                    } else {
                        this.datePicker = null;
                        this.timePicker = null;
                    }
                } else {
                    this.datePicker = null;
                    this.timePicker = null;
                }
            }
        }
    },
    methods: {
        createDayObject(date, currentMonth) {
            const dateStr = date.toISOString().split('T')[0];
            const today = new Date();
            const todayStr = today.toISOString().split('T')[0];

            let disabled = false;
            if (this.minDate && dateStr < this.minDate) {
                disabled = true;
            }
            if (this.maxDate && dateStr > this.maxDate) {
                disabled = true;
            }

            return {
                day: date.getDate(),
                date: dateStr,
                currentMonth,
                selected: this.datePicker === dateStr,
                isToday: dateStr === todayStr,
                disabled
            };
        },
        selectDate(dateStr) {
            if (!dateStr) return;
            this.datePicker = dateStr;
            this.updateDateTime();
        },
        previousMonth() {
            const newDate = new Date(this.currentDate);
            newDate.setMonth(newDate.getMonth() - 1);
            this.currentDate = newDate;
        },
        nextMonth() {
            const newDate = new Date(this.currentDate);
            newDate.setMonth(newDate.getMonth() + 1);
            this.currentDate = newDate;
        },
        updateDateTime() {
            if (this.datePicker) {
                const time = this.timePicker || '00:00';
                const [hours, minutes] = time.split(':');
                const dateTime = new Date(this.datePicker);
                dateTime.setHours(parseInt(hours) || 0);
                dateTime.setMinutes(parseInt(minutes) || 0);
                dateTime.setSeconds(0);
                dateTime.setMilliseconds(0);
                this.$emit('update:modelValue', dateTime.toISOString());
            } else {
                this.$emit('update:modelValue', null);
            }
        },
        clearValue() {
            this.datePicker = null;
            this.timePicker = null;
            this.$emit('update:modelValue', null);
            this.menuOpen = false;
        }
    }
};
</script>

<style scoped>
.calendar-container {
    width: 100%;
}

.time-field {
    width: 100%;
}

.calendar-header {
    padding: 0 2px;
}

.calendar-weekdays {
    margin-bottom: 2px;
}

.calendar-weekday {
    flex: 1;
    text-align: center;
    font-weight: 600;
    font-size: 9px;
    color: rgba(var(--v-theme-on-surface), 0.6);
    padding: 2px 1px;
}

.calendar-week {
    display: flex;
    margin-bottom: 1px;
}

.calendar-day {
    flex: 1;
    border: none;
    background: transparent;
    cursor: pointer;
    border-radius: 2px;
    font-size: 11px;
    color: rgba(var(--v-theme-on-surface), 0.87);
    transition: all 0.2s;
    margin: 0.5px;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 24px;
    min-width: 0;
    padding: 0;
}

.calendar-day:hover:not(:disabled) {
    background: rgba(var(--v-theme-primary), 0.1);
}

.calendar-day-other-month {
    color: rgba(var(--v-theme-on-surface), 0.3);
}

.calendar-day-selected {
    background: rgb(var(--v-theme-primary)) !important;
    color: rgb(var(--v-theme-on-primary)) !important;
    font-weight: 600;
}

.calendar-day-today {
    border: 1px solid rgb(var(--v-theme-primary));
    font-weight: 500;
}

.calendar-day-today.calendar-day-selected {
    border: 1px solid rgb(var(--v-theme-on-primary));
}

.calendar-day-disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.calendar-day-disabled:hover {
    background: transparent !important;
}
</style>
