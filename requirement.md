# Event Listing Page – Refactored Specification

## Overview

This document defines the refactored structure of the **Event Listing Page**, covering event types, database refactoring, form fields, permissions, and attendance tracking.

---

## Supported Event Types

The system supports the following event types:

* `admission`
* `exam`
* `student`
* `admin`

---

## Refactoring Notes

### Database Changes

* Remove `event_code` from the database.
* Each event must be uniquely identified using `event_id`.

---

## Event Form Structure

### Common Fields (Applicable to All Event Types)

These fields are mandatory for every event:

* `event_id`
* `name`
* `description`
* `active_status`
* `allow_exit`
* `allow_recurring`
* `priority`

---

## Event-Type Specific Fields

### Admission Events (`event_type = admission`)

Required additional field:

* `admission_exam_session_id`

---

### Exam Events (`event_type = exam`)

Required additional field:

* `exam_group_id`

---

### Student Events (`event_type = student`)

Required additional fields:

* `branch_code`
* `asession`
* `class`

---

### Admin Events (`event_type = admin`)

Required additional fields:

* `branch_code`

---

## Validation Rules

* Event-type–specific fields must be validated **only** when the corresponding `event_type` is selected.
* Backend and frontend validations must strictly enforce this rule.
* Storing irrelevant fields for an event type is not allowed.

---

## Event Permissions

### Table: `event_permissions`

#### Fields

* `event_permission_id`
* `event_id`
* `admin_id`

### Permission Rules

* Only the **creator of the event** can:

  * Grant permissions to other admins
  * Edit or revoke existing permissions
* Admins without permission:

  * Cannot edit the event
  * Cannot manage attendance for that event

### UI Requirements

* Permission management UI should be available on the event detail page.
* Admin selection should be searchable.
* Prevent duplicate permission assignments for the same admin and event.

---

## Event Attendance

### Table: `event_attendances`

#### Fields

* `event_attendance_id`
* `event_id`
* `user_code`
* `dated`
* `entry_time`
* `exit_time`

### `user_code` Definition

`user_code` can represent:

* Student `registerNo`
* Admin `username`
* Admission `form_no`

### Attendance Rules

* `entry_time` is mandatory.
* `exit_time` is optional and depends on `allow_exit`.
* Multiple attendance entries per event per day should be restricted unless explicitly allowed.

---

## Notes

* Attendance and permissions must be strictly tied to `event_id`.
* No legacy fields (`event_code`) should exist anywhere in the system.
* Schema and UI must remain aligned with this specification.

---

## End of Document
