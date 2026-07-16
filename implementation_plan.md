# Food Scanning App Architecture

Building a food scanning app is incredibly popular right now! To do this accurately, the mobile app needs to capture an image, and your Laravel backend needs to analyze that image to extract nutritional data.

Here is the best strategy for how we can build this into your current system.

## How It Works (The Flow)

1. **User Scans Food:** The Flutter app opens the camera, takes a photo of the meal, and uploads it to your Laravel API.
2. **AI Analysis:** Your Laravel backend receives the image and sends it to an AI Vision API.
3. **Data Extraction:** The AI identifies the food, estimates the portion size, and returns the Calories, Protein, Fat, Carbs, and Ingredients.
4. **Save Record:** Laravel saves this data to a `food_scans` table so the user can track their daily history.
5. **Return to App:** The Flutter app displays the nutritional breakdown to the user and asks the user to confirm the detected foods.
6. **User Approval:** Upon confirming, the app sends a confirmation to Laravel which then persists the record; if rejected, the user can retake or edit the scan.

---

## The Big Decision: Which AI/API to use?

To detect food from an image, we will integrate the **Base44 Vision API**, which provides high‑accuracy food recognition and nutritional estimation.

### Option 1: Base44 Vision API (Recommended)  
Send the image to Base44, which returns a JSON object containing the food name, estimated calories, protein, fat, carbs, and ingredients.
- **Pros:** Accurate multi‑item detection, built‑in nutrition estimation, and affordable per‑call pricing.
- **Cons:** Requires an API key and adherence to Base44 rate limits.

### Option 1: OpenAI GPT-4o (Vision) or Google Gemini Pro Vision ✨ *[Highly Recommended]*
Instead of using a rigid food database, you send the image to an AI like OpenAI (ChatGPT-4o). You prompt it: *"Analyze this image of food. Return a JSON object with the food name, estimated calories, protein, fat, carbs, and ingredients."*
- **Pros:** Incredibly smart. It can analyze a whole plate of mixed foods (e.g., a burger with fries) and estimate the total macros perfectly. It can also read nutrition labels if the user scans a barcode/label.
- **Cons:** Costs a few cents per API call.

### Option 2: Specialized Food Recognition APIs (LogMeal / CalorieMama)
These are APIs specifically trained *only* on food images. You send the image, they return the macros.
- **Pros:** Very fast and specifically built for this exact use-case.
- **Cons:** Often requires expensive monthly subscriptions (e.g., $50-$100/month).

### Option 3: Two-Step (Google Cloud Vision + Nutritionix/Edamam)
1. Send image to Google Cloud Vision to guess the object (e.g., "Apple").
2. Send the word "Apple" to a food database API (Edamam or USDA) to get the macros.
- **Pros:** Very cheap.
- **Cons:** Struggles with complex meals (like a bowl of soup or a mixed salad).

## Proposed Database Changes

We will need a new table to store the user's scan history.

### `create_food_scans_table`
- `id`
- `user_id` (Linked to the user who scanned)
- `image_path` (The URL of the uploaded photo)
- `food_name` (e.g., "Grilled Chicken Salad")
- `ingredients` (JSON array of detected ingredients)
- `calories` (Float)
- `protein` (Float)
- `fat` (Float)
- `carbs` (Float)


We’ll add a new screen `DashboardScreen` displaying:
- **Today's Summary**: total calories, protein, fat, carbs for scans created today.
- **Meal Timeline**: vertical list of scans (date, time, food name, thumbnail) with swipe to delete or edit.
- **Daily Ingredients**: aggregated list of unique ingredients consumed today.
- **Recent Scans**: horizontal carousel of the most recent 5 scans showing image preview and macro summary.

### Design Guidelines
- Use a dark glass‑morphism card with subtle gradient background.
- Primary accent color: `hsl(210, 70%, 55%)`; secondary accent: `hsl(340, 65%, 60%)`.
- Typography: Google Font **Inter** (fallback Roboto).
- Animations: Hero transition from camera capture to dashboard, fade‑in list items, micro‑scale on tap.
- Responsive: adapts to portrait & landscape, uses `LayoutBuilder`.

### Data Flow
1. Flutter calls `GET /api/food/scans?date=today` → returns array of scans.
2. Compute summary & ingredient aggregation locally **or** via new endpoint `GET /api/food/summary/today`.
3. Populate UI widgets; each scan card includes a button **Approve** (if not yet confirmed) that triggers `POST /api/food/scan/{id}/confirm`.

### Backend Additions
- `GET /api/food/scans` with optional `date` filter.
- `GET /api/food/summary/today` returning totals and unique ingredients.
- `POST /api/food/scan/{id}/confirm` to set a `confirmed` flag.


## Open Questions

> [!WARNING]
> Please confirm the following before we start building:
> 1. **Which AI do you want to use?** I strongly recommend **Option 1 (OpenAI Vision)** because it yields the most "magical" results for users, but let me know your preference.
> 2. **Should we store the images?** Do you want to save the actual photos the user takes to your server (so they can see a gallery of what they ate), or just discard the photo after the AI analyzes it to save server storage?

## Verification Plan
1. I will create the Database Migration and Model for `FoodScan`.
2. I will write the API endpoint `POST /api/food/scan` that accepts an image.
3. I will implement the AI integration to analyze the image and return the JSON.
