# Announcement Management Components

This directory contains the announcement management components for creating, editing, and displaying dynamic announcements on the website.

## File Structure

```
announcements/
├── AdminAnnouncements.vue           # Main component (table/list view) ~350 lines
├── AnnouncementFormDialog.vue       # Create/Edit form dialog ~1050 lines  
├── AnnouncementDetailsDialog.vue    # View/details dialog ~200 lines
└── README.md                        # This file
```

## Component Responsibilities

### AdminAnnouncements.vue
- Main table/list view of announcements
- Search and filter functionality (by status, type)
- Pagination
- Action buttons (view, edit, toggle status, delete)
- Opens form and details dialogs
- Bulk actions support

### AnnouncementFormDialog.vue
- Create/Edit announcement form
- Four tabs: Basic Info, Content, Settings & Schedule, SEO & Meta
- Rich text editor integration
- Image and video upload handling
- YouTube video URL support (auto-embed)
- Date/time picker for scheduling
- SEO suggestions and auto-generation
- Open Graph image support
- Form validation and submission

### AnnouncementDetailsDialog.vue
- Read-only view of announcement details
- Tabbed interface matching form structure
- Display all announcement information
- Edit button to switch to form dialog

## Key Features

### Dynamic Announcement Types
- **Company News**: General company updates and news
- **Offers / Promotions**: Special offers and promotional content
- **Events**: Event announcements and details
- **Holidays**: Holiday notifications
- **Urgent Alerts**: Time-sensitive critical information

### Display Types
- **Slider Banner**: Horizontal banner display
- **Popup**: Modal dialog display (auto-opens on page load)
- **Sidebar Ticker**: Sidebar scrolling ticker
- **Page Section**: Embedded page section

### Advanced Features

#### Media Support
- **Image Upload**: Support for announcement images with preview
- **Video Support**: 
  - Direct video file uploads (MP4, WebM)
  - YouTube video URLs (automatically converts to embed)
  - Supports formats: `youtube.com/watch?v=`, `youtu.be/`, etc.

#### Scheduling
- Start date/time scheduling
- End date/time scheduling
- Automatic activation/deactivation based on schedule
- Priority ordering (lower numbers = higher priority)

#### SEO Optimization
- **Auto-generated Meta Titles**: From announcement title
- **Auto-generated Meta Descriptions**: From short description
- **Auto-generated Keywords**: Extracted from title and description
- **Search Preview**: Live preview of search engine results
- **Character Count Indicators**: Color-coded validation (warning/error/success)
- **Open Graph Support**: Social media sharing optimization

#### Public Display
- Announcements automatically displayed as dialogs on public site
- Dismissible with "Don't show again" option
- Sequential display for multiple announcements
- Priority-based ordering
- Active/inactive status control

## Usage

```vue
<template>
    <AdminAnnouncements />
</template>

<script>
import AdminAnnouncements from './components/admin/announcements/AdminAnnouncements.vue';

export default {
    components: {
        AdminAnnouncements
    }
};
</script>
```

## Public Integration

### AnnouncementsBanner.vue
The public-facing announcement display component (`resources/js/components/public/announcements/AnnouncementsBanner.vue`) automatically:
- Fetches active announcements from API
- Displays as modal dialogs
- Supports YouTube video embeds
- Handles image and video media
- Tracks dismissed announcements in localStorage
- Shows announcements sequentially with pagination

### API Endpoints

#### Admin API
- `GET /api/v1/announcements` - List announcements (paginated, filtered)
- `POST /api/v1/announcements` - Create new announcement
- `GET /api/v1/announcements/{id}` - Get single announcement
- `PUT /api/v1/announcements/{id}` - Update announcement
- `DELETE /api/v1/announcements/{id}` - Delete announcement (soft delete)
- `PUT /api/v1/announcements/{id}/toggle-status` - Toggle active status

#### Public API
- `GET /api/openapi/announcements` - Get active announcements (public)
- `GET /api/openapi/announcements/{slug}` - Get announcement by slug (public)

## Data Model

### Announcement Fields
- `title` (required): Announcement title
- `slug` (required): URL-friendly identifier
- `short_description`: Brief description for listings
- `content`: Full HTML content (rich text)
- `type`: Announcement type (company_news, offers_promotions, etc.)
- `display_type`: Display method (slider_banner, popup, etc.)
- `image`: Image URL or path
- `video`: Video URL (supports YouTube links)
- `external_link`: Optional external link
- `open_in_new_tab`: Whether external link opens in new tab
- `start_date`: Scheduling start date/time
- `end_date`: Scheduling end date/time
- `is_active`: Active status
- `priority`: Display priority (0-999, lower = higher priority)
- `language`: Language code (optional, for multi-language)
- `meta_title`: SEO meta title
- `meta_description`: SEO meta description
- `meta_keywords`: SEO keywords
- `og_image`: Open Graph image for social sharing

## Future Improvements

- Extract tab components if needed (AnnouncementFormBasicTab, AnnouncementFormContentTab, etc.)
- Extract form validation into composables
- Extract image/video upload logic into composables
- Create reusable editor component
- Add announcement templates/presets
- Add bulk import/export functionality
- Add announcement analytics (views, clicks)
- Support for multiple languages (i18n)
- Add announcement A/B testing
- Rich notification system for urgent alerts

