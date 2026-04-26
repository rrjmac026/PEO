# Laravel to MERN Stack Migration Guide

## Executive Overview
Your current Laravel application is a workflow management system with:
- **Role-based access** (Admin, Reviewer, User)
- **Complex workflows** (Work Requests, Concrete Pouring)
- **Notifications system**
- **File generation** (PDF, Excel)
- **Email notifications**
- **Employee management**

This migration will take your monolithic Laravel app and split it into:
- **Backend**: Node.js/Express API (REST/GraphQL)
- **Frontend**: React Single Page Application (SPA)
- **Database**: MongoDB or PostgreSQL (your choice)

---

## PHASE 1: Planning & Preparation (Days 1-2)

### Step 1.1: Decide on Database
**Option A: MongoDB** (Better for rapid prototyping, flexible schema)
- Pros: Document-based, flexible, JSON-like
- Cons: No built-in relationships, requires careful design

**Option B: PostgreSQL** (Keep relational DB, similar to Laravel)
- Pros: Familiar structure, strong relationships, ACID compliance
- Cons: Setup takes longer, more rigid

**Recommendation for your app**: PostgreSQL (keep your existing database structure, easier mapping)

### Step 1.2: Inventory Your Application
Create a detailed audit:

```
MODELS (Database Tables):
- User ✓ (with roles)
- Employee ✓
- WorkRequest ✓ (with status workflow)
- ConcretePouring ✓ (with status workflow)
- Notification ✓
- WorkRequestLog ✓

APIS/ENDPOINTS:
- Admin: Dashboard, Work Requests, Employees
- User: Create Work Requests, View Concrete Pouring
- Reviewer: Review & approve workflows
- Notifications: Get, mark read

FEATURES:
- Authentication & Authorization
- Workflow state management
- File generation (PDF, Excel)
- Email notifications
- Search functionality

EXTERNAL SERVICES:
- Mail service
- PDF generation (DomPDF → will use pdf-lib or react-pdf)
- Excel export (PhpSpreadsheet → will use xlsx or exceljs)
```

### Step 1.3: Create Migration Timeline
- **Week 1**: Backend setup + Database migration
- **Week 2**: API endpoints + Authentication
- **Week 3**: Frontend setup + Core pages
- **Week 4**: Advanced features + Notifications
- **Week 5**: Testing + Deployment

---

## PHASE 2: Backend Setup (Days 3-5)

### Step 2.1: Initialize Node.js/Express Project

```bash
# Create new backend project
mkdir peo-backend
cd peo-backend

# Initialize Node project
npm init -y

# Install core dependencies
npm install express cors dotenv axios bcryptjs jsonwebtoken mongoose sequelize pg pg-hstore
npm install nodemailer multer express-validator helmet compression

# Install dev dependencies
npm install --save-dev nodemon jest supertest @types/node
```

### Step 2.2: Create Project Structure

```
peo-backend/
├── src/
│   ├── config/
│   │   ├── database.js
│   │   ├── auth.js
│   │   └── mail.js
│   ├── models/
│   │   ├── User.js
│   │   ├── Employee.js
│   │   ├── WorkRequest.js
│   │   ├── ConcretePouring.js
│   │   ├── Notification.js
│   │   └── WorkRequestLog.js
│   ├── controllers/
│   │   ├── admin/
│   │   ├── user/
│   │   ├── reviewer/
│   │   ├── authController.js
│   │   └── notificationController.js
│   ├── routes/
│   │   ├── admin.js
│   │   ├── user.js
│   │   ├── reviewer.js
│   │   ├── auth.js
│   │   └── index.js
│   ├── middleware/
│   │   ├── auth.js
│   │   ├── roleCheck.js
│   │   └── errorHandler.js
│   ├── services/
│   │   ├── PDFService.js
│   │   ├── ExcelService.js
│   │   ├── MailService.js
│   │   ├── NotificationService.js
│   │   └── WorkflowService.js
│   ├── utils/
│   │   └── validators.js
│   └── app.js
├── .env
├── .env.example
├── server.js
├── package.json
└── README.md
```

### Step 2.3: Database Migration

**Using Sequelize (ORM for PostgreSQL):**

```bash
npm install sequelize-cli
npx sequelize-cli init
```

**Create Migrations:**
```javascript
// Create User migration
npx sequelize-cli model:generate --name User --attributes email:string,name:string,password:string,role:string,email_verified_at:date

// Create other models similarly
npx sequelize-cli model:generate --name Employee --attributes name:string,email:string,position:string
npx sequelize-cli model:generate --name WorkRequest --attributes status:string,employee_id:integer,assigned_to:integer,created_by:integer
npx sequelize-cli model:generate --name ConcretePouring --attributes status:string,work_request_id:integer
npx sequelize-cli model:generate --name Notification --attributes user_id:integer,type:string,data:json,read_at:date
npx sequelize-cli model:generate --name WorkRequestLog --attributes work_request_id:integer,action:string,user_id:integer
```

**Run migrations:**
```bash
npx sequelize-cli db:migrate
```

---

## PHASE 3: Backend API Development (Days 6-9)

### Step 3.1: Authentication System

**Create JWT Authentication:**

```javascript
// src/middleware/auth.js
const jwt = require('jsonwebtoken');

const authMiddleware = (req, res, next) => {
  const token = req.headers.authorization?.split(' ')[1];
  
  if (!token) return res.status(401).json({ error: 'Unauthorized' });
  
  try {
    const decoded = jwt.verify(token, process.env.JWT_SECRET);
    req.user = decoded;
    next();
  } catch (err) {
    res.status(401).json({ error: 'Invalid token' });
  }
};

module.exports = authMiddleware;
```

```javascript
// src/middleware/roleCheck.js
const roleCheck = (requiredRole) => {
  return (req, res, next) => {
    if (req.user.role !== requiredRole) {
      return res.status(403).json({ error: 'Forbidden' });
    }
    next();
  };
};

module.exports = roleCheck;
```

### Step 3.2: Convert Laravel Controllers → Express Controllers

**Example: WorkRequest Controller**

```javascript
// src/controllers/admin/workRequestController.js
const { WorkRequest, Employee, User } = require('../../models');

exports.index = async (req, res) => {
  try {
    const workRequests = await WorkRequest.findAll({
      include: [{ model: Employee }, { model: User, as: 'assignedTo' }]
    });
    res.json(workRequests);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
};

exports.show = async (req, res) => {
  try {
    const workRequest = await WorkRequest.findByPk(req.params.id, {
      include: [{ model: Employee }, { model: User }]
    });
    if (!workRequest) return res.status(404).json({ error: 'Not found' });
    res.json(workRequest);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
};

exports.store = async (req, res) => {
  try {
    const workRequest = await WorkRequest.create(req.body);
    res.status(201).json(workRequest);
  } catch (err) {
    res.status(400).json({ error: err.message });
  }
};

exports.update = async (req, res) => {
  try {
    const workRequest = await WorkRequest.findByPk(req.params.id);
    if (!workRequest) return res.status(404).json({ error: 'Not found' });
    await workRequest.update(req.body);
    res.json(workRequest);
  } catch (err) {
    res.status(400).json({ error: err.message });
  }
};

exports.destroy = async (req, res) => {
  try {
    const workRequest = await WorkRequest.findByPk(req.params.id);
    if (!workRequest) return res.status(404).json({ error: 'Not found' });
    await workRequest.destroy();
    res.json({ message: 'Deleted' });
  } catch (err) {
    res.status(400).json({ error: err.message });
  }
};
```

### Step 3.3: Setup API Routes

```javascript
// src/routes/admin.js
const express = require('express');
const router = express.Router();
const authMiddleware = require('../middleware/auth');
const roleCheck = require('../middleware/roleCheck');
const workRequestController = require('../controllers/admin/workRequestController');

router.use(authMiddleware, roleCheck('admin'));

router.get('/work-requests', workRequestController.index);
router.post('/work-requests', workRequestController.store);
router.get('/work-requests/:id', workRequestController.show);
router.put('/work-requests/:id', workRequestController.update);
router.delete('/work-requests/:id', workRequestController.destroy);

// Add more routes...

module.exports = router;
```

### Step 3.4: Implement Service Layer

```javascript
// src/services/PDFService.js
const PDFDocument = require('pdfkit');
const fs = require('fs');

class PDFService {
  static async generateWorkRequestPDF(workRequest) {
    return new Promise((resolve, reject) => {
      const doc = new PDFDocument();
      const path = `./storage/pdfs/${workRequest.id}.pdf`;
      const stream = fs.createWriteStream(path);

      doc.pipe(stream);
      doc.fontSize(20).text('Work Request');
      doc.fontSize(12).text(`ID: ${workRequest.id}`);
      doc.text(`Status: ${workRequest.status}`);
      // Add more content...
      doc.end();

      stream.on('finish', () => resolve(path));
      stream.on('error', reject);
    });
  }
}

module.exports = PDFService;
```

---

## PHASE 4: Frontend Setup (Days 10-12)

### Step 4.1: Create React App

```bash
# Using Vite (faster than CRA)
npm create vite@latest peo-frontend -- --template react
cd peo-frontend

# Install dependencies
npm install axios react-router-dom zustand react-query tailwindcss postcss autoprefixer
npm install -D tailwindcss postcss autoprefixer
```

### Step 4.2: Project Structure

```
peo-frontend/
├── src/
│   ├── components/
│   │   ├── Layout/
│   │   │   ├── Header.jsx
│   │   │   ├── Sidebar.jsx
│   │   │   └── Layout.jsx
│   │   ├── WorkRequest/
│   │   │   ├── WorkRequestList.jsx
│   │   │   ├── WorkRequestForm.jsx
│   │   │   └── WorkRequestDetail.jsx
│   │   ├── ConcretePouring/
│   │   │   ├── ConcretePouringList.jsx
│   │   │   ├── ConcretePouringForm.jsx
│   │   │   └── ConcretePouringDetail.jsx
│   │   ├── Admin/
│   │   │   ├── Dashboard.jsx
│   │   │   ├── EmployeeManagement.jsx
│   │   │   └── UserManagement.jsx
│   │   ├── Auth/
│   │   │   ├── Login.jsx
│   │   │   ├── Register.jsx
│   │   │   └── ProtectedRoute.jsx
│   │   └── Notifications/
│   │       ├── NotificationCenter.jsx
│   │       └── NotificationBell.jsx
│   ├── pages/
│   │   ├── Dashboard.jsx
│   │   ├── Login.jsx
│   │   ├── NotFound.jsx
│   │   └── [feature pages]
│   ├── services/
│   │   ├── api.js
│   │   ├── authService.js
│   │   ├── workRequestService.js
│   │   ├── concretePouringService.js
│   │   └── notificationService.js
│   ├── stores/
│   │   ├── authStore.js
│   │   ├── notificationStore.js
│   │   └── uiStore.js
│   ├── hooks/
│   │   ├── useAuth.js
│   │   └── useNotifications.js
│   ├── utils/
│   │   ├── constants.js
│   │   └── helpers.js
│   ├── App.jsx
│   └── main.jsx
├── .env.local
├── tailwind.config.js
├── vite.config.js
└── package.json
```

### Step 4.3: Setup API Service Layer

```javascript
// src/services/api.js
import axios from 'axios';

const API_BASE = import.meta.env.VITE_API_URL || 'http://localhost:5000/api';

const api = axios.create({
  baseURL: API_BASE,
  headers: {
    'Content-Type': 'application/json'
  }
});

// Add token to requests
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Handle responses
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default api;
```

### Step 4.4: State Management (Zustand)

```javascript
// src/stores/authStore.js
import { create } from 'zustand';

export const useAuthStore = create((set) => ({
  user: null,
  token: localStorage.getItem('token') || null,
  isAuthenticated: !!localStorage.getItem('token'),

  login: (user, token) => {
    localStorage.setItem('token', token);
    set({ user, token, isAuthenticated: true });
  },

  logout: () => {
    localStorage.removeItem('token');
    set({ user: null, token: null, isAuthenticated: false });
  },

  setUser: (user) => set({ user })
}));
```

### Step 4.5: Routing Setup

```javascript
// src/App.jsx
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { useAuthStore } from './stores/authStore';
import ProtectedRoute from './components/Auth/ProtectedRoute';
import Login from './pages/Login';
import Dashboard from './pages/Dashboard';
import WorkRequestList from './components/WorkRequest/WorkRequestList';
import AdminDashboard from './components/Admin/Dashboard';
import Layout from './components/Layout/Layout';

function App() {
  const { isAuthenticated, user } = useAuthStore();

  return (
    <BrowserRouter>
      <Routes>
        <Route path="/login" element={<Login />} />
        
        <Route
          path="/"
          element={
            isAuthenticated ? (
              <Layout>
                <Routes>
                  <Route path="/" element={<Dashboard />} />
                  <Route path="/work-requests" element={<WorkRequestList />} />
                  {user?.role === 'admin' && (
                    <Route path="/admin/*" element={<AdminDashboard />} />
                  )}
                </Routes>
              </Layout>
            ) : (
              <Navigate to="/login" />
            )
          }
        />
      </Routes>
    </BrowserRouter>
  );
}

export default App;
```

---

## PHASE 5: Feature Migration (Days 13-18)

### Step 5.1: Notifications System

**Backend:**
```javascript
// src/services/NotificationService.js
class NotificationService {
  static async sendNotification(userId, type, data) {
    return await Notification.create({
      user_id: userId,
      type,
      data: JSON.stringify(data)
    });
  }

  static async getUserNotifications(userId) {
    return await Notification.findAll({
      where: { user_id: userId },
      order: [['createdAt', 'DESC']]
    });
  }

  static async markAsRead(notificationId) {
    return await Notification.update(
      { read_at: new Date() },
      { where: { id: notificationId } }
    );
  }
}

module.exports = NotificationService;
```

**Frontend:**
```javascript
// src/hooks/useNotifications.js
import { useEffect, useState } from 'react';
import notificationService from '../services/notificationService';

export const useNotifications = () => {
  const [notifications, setNotifications] = useState([]);

  useEffect(() => {
    const fetchNotifications = async () => {
      try {
        const data = await notificationService.getAll();
        setNotifications(data);
      } catch (err) {
        console.error(err);
      }
    };

    fetchNotifications();
    const interval = setInterval(fetchNotifications, 30000); // Poll every 30s

    return () => clearInterval(interval);
  }, []);

  return { notifications };
};
```

### Step 5.2: File Generation (PDF/Excel)

**Backend (PDF):**
```bash
npm install pdfkit xlsx
```

```javascript
// Generate PDF endpoint
router.get('/work-requests/:id/pdf', async (req, res) => {
  try {
    const workRequest = await WorkRequest.findByPk(req.params.id);
    const pdf = await PDFService.generateWorkRequestPDF(workRequest);
    res.download(pdf);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});
```

**Frontend (Download):**
```javascript
const downloadPDF = async (workRequestId) => {
  const response = await api.get(`/work-requests/${workRequestId}/pdf`, {
    responseType: 'blob'
  });
  const url = window.URL.createObjectURL(new Blob([response.data]));
  const link = document.createElement('a');
  link.href = url;
  link.setAttribute('download', `work-request-${workRequestId}.pdf`);
  document.body.appendChild(link);
  link.click();
};
```

---

## PHASE 6: Testing & Deployment (Days 19-21)

### Step 6.1: Backend Testing

```bash
npm install --save-dev jest supertest
```

```javascript
// tests/workRequest.test.js
describe('WorkRequest API', () => {
  it('should get all work requests', async () => {
    const response = await request(app).get('/api/work-requests');
    expect(response.status).toBe(200);
  });

  it('should create a work request', async () => {
    const response = await request(app)
      .post('/api/work-requests')
      .set('Authorization', `Bearer ${token}`)
      .send({ employee_id: 1, status: 'pending' });
    expect(response.status).toBe(201);
  });
});
```

### Step 6.2: Frontend Testing

```bash
npm install --save-dev vitest @testing-library/react
```

### Step 6.3: Deployment

**Backend (Node.js):**
- Deploy to: Heroku, Railway, Vercel, AWS, DigitalOcean
- Use: PM2 for process management
- Setup: Environment variables, database connection

**Frontend (React):**
- Build: `npm run build`
- Deploy to: Netlify, Vercel, AWS S3 + CloudFront, GitHub Pages

---

## Migration Checklist

- [ ] Database design & migrations complete
- [ ] Backend project initialized
- [ ] Authentication system implemented
- [ ] API endpoints created (CRUD for all models)
- [ ] Role-based access control working
- [ ] Frontend project initialized
- [ ] Routing and layout setup
- [ ] API service layer complete
- [ ] State management implemented
- [ ] Core pages migrated (Dashboard, Work Requests, etc.)
- [ ] Admin features implemented
- [ ] Notifications system working
- [ ] PDF/Excel generation functional
- [ ] Email notifications integrated
- [ ] Search/filter functionality added
- [ ] Unit tests written
- [ ] Integration tests written
- [ ] Performance optimized
- [ ] Security audit completed
- [ ] Staging environment tested
- [ ] Production deployment

---

## Key Differences to Remember

| Laravel | Node.js/React |
|---------|---------------|
| Monolithic | Separated backend/frontend |
| Blade templates | React components |
| Eloquent ORM | Sequelize/Prisma |
| Laravel middleware | Express middleware |
| Session/Cookie auth | JWT tokens |
| Built-in validation | Manual/library validation |
| Artisan commands | Custom scripts/npm |

---

## Common Pitfalls to Avoid

1. **Don't try to migrate everything at once** - Do it feature by feature
2. **Don't forget environment variables** - Setup `.env` files properly
3. **Don't skip authentication** - Implement JWT early
4. **Don't ignore CORS** - Configure properly for local/prod
5. **Don't forget error handling** - Use consistent error responses
6. **Don't skip database backups** - Backup before migration
7. **Don't use hardcoded values** - Use config files

---

## Next Steps

1. **Start with backend infrastructure** (Node + Express + Database)
2. **Migrate authentication system** first
3. **Build API endpoints** for existing features
4. **Build frontend from scratch** with React
5. **Test thoroughly** before deploying
6. **Migrate data** carefully
7. **Run both systems in parallel** during transition period

Would you like me to create starter code for any specific part?
