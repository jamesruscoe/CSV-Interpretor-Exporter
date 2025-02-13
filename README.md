# Name Processing Application

A Laravel-based application that processes CSV files containing name data and splits them into structured individual records. The application handles various name formats and configurations, making it ideal for cleaning and standardizing name data.

## Features

- CSV file upload and processing
- Handles multiple name formats:
  - Standard names (e.g., "Mr John Smith")
  - Names with initials (e.g., "Mr J. Smith", "Dr P Gunn")
  - Multiple people in one entry (e.g., "Mr and Mrs Smith")
  - Compound titles (e.g., "Dr and Mrs Joe Bloggs")
  - Double-barreled surnames (e.g., "Mrs Hughes-Eastwood")
  - Full word titles (e.g., "Mister" → "Mr", "Doctor" → "Dr")

## Installation

1. Clone the repository:
```bash
git clone git@github.com:jamesruscoe/CSV-Interpretor-Exporter.git
cd /CSV-Interpretor-Exporter
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Set up environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure your database in `.env`

5. Run migrations:
```bash
php artisan migrate
```

6. Build assets:
```bash
npm run dev
```

## Usage

1. Start the application:
```bash
php artisan serve
```

2. Navigate to `http://localhost:8000` in your browser

3. Upload a CSV file containing name data

4. The application will process the file and display the results in a structured table

### CSV Format

The input CSV should have a header row and contain names in any of these formats:
- Mr John Smith
- Mrs Jane Smith
- Dr P Gunn
- Mr and Mrs Smith
- Mister John Doe
- Mr F. Fredrickson
- Prof Alex Brogan
- Mrs Hughes-Eastwood

### Output Format

The application processes names into the following structure:
```php
[
    'title' => string|null,      // e.g., "Mr", "Mrs", "Dr", "Prof"
    'first_name' => string|null, // e.g., "John", "Jane"
    'initial' => string|null,    // e.g., "P", "F"
    'last_name' => string|null   // e.g., "Smith", "Hughes-Eastwood"
]
```

## Technical Details

### Core Components

- `UploadController`: Handles file uploads and initiates processing
- `UploadService`: Contains the core name processing logic
- Frontend interface built with Bootstrap 5
- CSRF protection enabled
- Drag-and-drop file upload support

### Name Processing Logic

The application uses a sophisticated parsing system that:
1. Identifies and normalizes titles
2. Separates multiple people in a single entry
3. Handles initials and first names appropriately
4. Preserves and properly formats compound surnames
5. Maintains proper capitalization

## License

This project is licensed under the MIT License - see the LICENSE file for details.
