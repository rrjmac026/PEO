<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Work Request</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #0f1117;
      --surface: #181c27;
      --surface2: #1e2335;
      --border: #2a3050;
      --accent: #4f8dff;
      --accent2: #00d4aa;
      --accent3: #ff6b6b;
      --text: #e8eaf6;
      --muted: #7c85a8;
      --label: #a8b3d8;
      --success: #00d4aa;
      --error: #ff6b6b;
      --radius: 12px;
      --radius-sm: 8px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      padding: 0;
      overflow-x: hidden;
    }

    /* Animated background */
    body::before {
      content: '';
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background:
        radial-gradient(ellipse 60% 40% at 20% 10%, rgba(79,141,255,0.08) 0%, transparent 60%),
        radial-gradient(ellipse 50% 30% at 80% 80%, rgba(0,212,170,0.06) 0%, transparent 60%);
      pointer-events: none;
      z-index: 0;
    }

    /* Header */
    .header {
      position: sticky;
      top: 0;
      z-index: 100;
      background: rgba(15,17,23,0.85);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--border);
      padding: 0 2rem;
    }
    .header-inner {
      max-width: 860px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 64px;
    }
    .header-left {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .back-btn {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 6px 14px;
      background: var(--surface2);
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      color: var(--muted);
      text-decoration: none;
      font-size: 13px;
      font-weight: 500;
      transition: all 0.2s;
      cursor: pointer;
    }
    .back-btn:hover { color: var(--text); border-color: var(--accent); background: rgba(79,141,255,0.08); }
    .header-title {
      font-family: 'Syne', sans-serif;
      font-size: 17px;
      font-weight: 700;
      color: var(--text);
      letter-spacing: -0.3px;
    }

    /* Progress indicator */
    .progress-bar {
      position: sticky;
      top: 64px;
      z-index: 99;
      background: rgba(15,17,23,0.9);
      backdrop-filter: blur(10px);
      padding: 12px 2rem;
      border-bottom: 1px solid var(--border);
    }
    .progress-inner {
      max-width: 860px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      gap: 0;
    }
    .step-item {
      display: flex;
      align-items: center;
      gap: 8px;
      flex: 1;
      cursor: pointer;
      position: relative;
    }
    .step-item:not(:last-child)::after {
      content: '';
      position: absolute;
      right: 0;
      top: 50%;
      transform: translateY(-50%);
      width: calc(100% - 36px);
      left: 36px;
      height: 1px;
      background: var(--border);
      z-index: -1;
      transition: background 0.4s;
    }
    .step-item.done:not(:last-child)::after { background: var(--accent); }
    .step-num {
      width: 28px; height: 28px;
      border-radius: 50%;
      border: 2px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      font-size: 12px; font-weight: 700;
      color: var(--muted);
      background: var(--bg);
      transition: all 0.3s;
      flex-shrink: 0;
      font-family: 'Syne', sans-serif;
      position: relative;
      z-index: 1;
    }
    .step-item.active .step-num {
      background: var(--accent);
      border-color: var(--accent);
      color: white;
      box-shadow: 0 0 16px rgba(79,141,255,0.5);
    }
    .step-item.done .step-num {
      background: var(--accent2);
      border-color: var(--accent2);
      color: white;
    }
    .step-label {
      font-size: 11px;
      font-weight: 500;
      color: var(--muted);
      white-space: nowrap;
      transition: color 0.3s;
      display: none;
    }
    .step-item.active .step-label { color: var(--accent); }
    .step-item.done .step-label { color: var(--accent2); }
    @media (min-width: 600px) { .step-label { display: block; } }

    /* Main content */
    .main {
      max-width: 860px;
      margin: 0 auto;
      padding: 2.5rem 2rem 4rem;
      position: relative;
      z-index: 1;
    }

    /* Section panels */
    .section-panel {
      display: none;
      animation: fadeSlide 0.35s ease both;
    }
    .section-panel.active { display: block; }
    @keyframes fadeSlide {
      from { opacity: 0; transform: translateY(18px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .panel-header {
      margin-bottom: 2rem;
    }
    .panel-tag {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: var(--accent);
      background: rgba(79,141,255,0.1);
      border: 1px solid rgba(79,141,255,0.2);
      padding: 4px 10px;
      border-radius: 20px;
      margin-bottom: 10px;
    }
    .panel-tag.green { color: var(--accent2); background: rgba(0,212,170,0.1); border-color: rgba(0,212,170,0.2); }
    .panel-tag.orange { color: #ffaa4f; background: rgba(255,170,79,0.1); border-color: rgba(255,170,79,0.2); }
    .panel-tag.purple { color: #b48fff; background: rgba(180,143,255,0.1); border-color: rgba(180,143,255,0.2); }

    .panel-title {
      font-family: 'Syne', sans-serif;
      font-size: 26px;
      font-weight: 800;
      color: var(--text);
      letter-spacing: -0.5px;
      line-height: 1.2;
      margin-bottom: 6px;
    }
    .panel-sub {
      font-size: 14px;
      color: var(--muted);
      font-weight: 300;
    }

    /* Card grid */
    .fields-grid {
      display: grid;
      gap: 16px;
    }
    .fields-grid.two-col { grid-template-columns: 1fr 1fr; }

    /* Field */
    .field {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }
    label {
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: var(--label);
      display: flex;
      align-items: center;
      gap: 4px;
    }
    .req { color: var(--accent3); font-size: 14px; line-height: 1; }

    .input-wrap {
      position: relative;
    }
    .input-icon {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--muted);
      font-size: 14px;
      pointer-events: none;
      transition: color 0.2s;
    }
    textarea + .input-icon,
    .input-wrap.textarea-wrap .input-icon {
      top: 16px;
      transform: none;
    }

    input, select, textarea {
      width: 100%;
      background: var(--surface);
      border: 1.5px solid var(--border);
      border-radius: var(--radius-sm);
      color: var(--text);
      font-family: 'DM Sans', sans-serif;
      font-size: 14px;
      font-weight: 400;
      padding: 12px 14px;
      outline: none;
      transition: all 0.2s;
      appearance: none;
    }
    .has-icon input, .has-icon select, .has-icon textarea {
      padding-left: 38px;
    }
    input:focus, select:focus, textarea:focus {
      border-color: var(--accent);
      background: var(--surface2);
      box-shadow: 0 0 0 3px rgba(79,141,255,0.12);
    }
    input:focus ~ .input-icon, select:focus ~ .input-icon, textarea:focus ~ .input-icon,
    .input-wrap:focus-within .input-icon { color: var(--accent); }
    input[readonly] {
      background: rgba(255,255,255,0.03);
      color: var(--muted);
      cursor: not-allowed;
      border-style: dashed;
    }
    select option { background: var(--surface2); }
    textarea { resize: vertical; min-height: 100px; }

    input.error, select.error, textarea.error {
      border-color: var(--error);
      box-shadow: 0 0 0 3px rgba(255,107,107,0.1);
    }
    .err-msg {
      font-size: 11px;
      color: var(--error);
      display: none;
      align-items: center;
      gap: 4px;
    }
    .err-msg.show { display: flex; }

    /* Special: readonly badge */
    .readonly-badge {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 10px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: var(--muted);
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 4px;
      padding: 2px 7px;
    }

    /* Hint */
    .field-hint {
      font-size: 11px;
      color: var(--muted);
      font-style: italic;
    }

    /* Navigation buttons */
    .nav-actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 2.5rem;
      padding-top: 2rem;
      border-top: 1px solid var(--border);
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 24px;
      border: none;
      border-radius: var(--radius-sm);
      font-family: 'Syne', sans-serif;
      font-size: 13px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      cursor: pointer;
      transition: all 0.22s;
      text-decoration: none;
    }
    .btn-ghost {
      background: transparent;
      border: 1.5px solid var(--border);
      color: var(--muted);
    }
    .btn-ghost:hover { border-color: var(--accent); color: var(--accent); background: rgba(79,141,255,0.06); }

    .btn-primary {
      background: var(--accent);
      color: white;
      box-shadow: 0 4px 20px rgba(79,141,255,0.3);
    }
    .btn-primary:hover { background: #6ba3ff; box-shadow: 0 6px 28px rgba(79,141,255,0.45); transform: translateY(-1px); }
    .btn-primary:active { transform: translateY(0); }

    .btn-success {
      background: var(--accent2);
      color: #0f1117;
      box-shadow: 0 4px 20px rgba(0,212,170,0.3);
    }
    .btn-success:hover { background: #00e8bb; box-shadow: 0 6px 28px rgba(0,212,170,0.4); transform: translateY(-1px); }

    .btn:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }

    /* Summary card for final review */
    .summary-grid {
      display: grid;
      gap: 12px;
    }
    .summary-section {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      overflow: hidden;
    }
    .summary-section-head {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 12px 16px;
      background: var(--surface2);
      border-bottom: 1px solid var(--border);
    }
    .summary-section-head span {
      font-family: 'Syne', sans-serif;
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: var(--label);
    }
    .summary-rows {
      padding: 12px 16px;
      display: grid;
      gap: 10px;
    }
    .summary-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 12px;
    }
    .summary-key {
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.7px;
      color: var(--muted);
      min-width: 140px;
    }
    .summary-val {
      font-size: 13px;
      color: var(--text);
      text-align: right;
      word-break: break-word;
    }
    .summary-val.empty { color: var(--muted); font-style: italic; }

    /* Status select visuals */
    .status-options {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
      gap: 8px;
      margin-top: 4px;
    }
    .status-opt {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 10px 12px;
      border: 1.5px solid var(--border);
      border-radius: var(--radius-sm);
      cursor: pointer;
      transition: all 0.2s;
      background: var(--surface);
    }
    .status-opt:hover { border-color: var(--accent); background: rgba(79,141,255,0.05); }
    .status-opt.selected { border-color: var(--accent); background: rgba(79,141,255,0.1); }
    .status-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .status-opt-label { font-size: 13px; font-weight: 500; color: var(--text); }
    .status-opt input[type=radio] { display: none; }

    /* Success screen */
    .success-screen {
      display: none;
      text-align: center;
      padding: 4rem 2rem;
      animation: fadeSlide 0.5s ease both;
    }
    .success-icon {
      width: 80px; height: 80px;
      background: rgba(0,212,170,0.12);
      border: 2px solid var(--accent2);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 1.5rem;
      font-size: 32px;
      box-shadow: 0 0 40px rgba(0,212,170,0.2);
      animation: pulse 2s ease infinite;
    }
    @keyframes pulse {
      0%, 100% { box-shadow: 0 0 40px rgba(0,212,170,0.2); }
      50% { box-shadow: 0 0 60px rgba(0,212,170,0.35); }
    }
    .success-title {
      font-family: 'Syne', sans-serif;
      font-size: 28px;
      font-weight: 800;
      color: var(--text);
      margin-bottom: 8px;
    }
    .success-sub { font-size: 15px; color: var(--muted); max-width: 360px; margin: 0 auto 2rem; line-height: 1.6; }
    .req-id {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: var(--surface2);
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      padding: 10px 20px;
      font-family: 'Syne', sans-serif;
      font-size: 18px;
      font-weight: 700;
      color: var(--accent2);
      letter-spacing: 2px;
      margin-bottom: 2rem;
    }

    /* Floating save indicator */
    .floating-indicator {
      position: fixed;
      bottom: 24px;
      right: 24px;
      display: flex;
      align-items: center;
      gap: 8px;
      background: var(--surface2);
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 8px 16px;
      font-size: 11px;
      color: var(--muted);
      z-index: 200;
      opacity: 0;
      transform: translateY(8px);
      transition: all 0.3s;
    }
    .floating-indicator.show { opacity: 1; transform: translateY(0); }
    .floating-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--accent2); animation: blink 1.5s ease infinite; }
    @keyframes blink { 0%,100% { opacity: 1; } 50% { opacity: 0.3; } }

    @media (max-width: 600px) {
      .fields-grid.two-col { grid-template-columns: 1fr; }
      .panel-title { font-size: 22px; }
      .main { padding: 1.5rem 1rem 4rem; }
      .status-options { grid-template-columns: 1fr 1fr; }
    }
  </style>
</head>
    <body>

    <!-- Header -->
    <header class="header">
    <div class="header-inner">
        <div class="header-left">
        <button class="back-btn" onclick="history.back()">
            ← Back
        </button>
        <span class="header-title">Work Request</span>
        </div>
        <span style="font-size:12px;color:var(--muted);">Draft auto-saved</span>
    </div>
    </header>

    <!-- Progress Steps -->
    <div class="progress-bar">
    <div class="progress-inner">
        <div class="step-item active" id="step-1" onclick="goToStep(1)">
        <div class="step-num">1</div>
        <span class="step-label">Project Info</span>
        </div>
        <div class="step-item" id="step-2" onclick="goToStep(2)">
        <div class="step-num">2</div>
        <span class="step-label">Request Details</span>
        </div>
        <div class="step-item" id="step-3" onclick="goToStep(3)">
        <div class="step-num">3</div>
        <span class="step-label">Pay Items</span>
        </div>
        <div class="step-item" id="step-4" onclick="goToStep(4)">
        <div class="step-num">4</div>
        <span class="step-label">Review & Submit</span>
        </div>
    </div>
    </div>

    <main class="main">
    <form id="wr-form" novalidate>

        <!-- STEP 1: Project Info -->
        <div class="section-panel active" id="panel-1">
        <div class="panel-header">
            <div class="panel-tag">📁 Step 1 of 4</div>
            <h2 class="panel-title">Project Information</h2>
            <p class="panel-sub">Tell us about the project where the work will take place.</p>
        </div>

        <div class="fields-grid">
            <div class="field">
            <label for="name_of_project">Project Name <span class="req">*</span></label>
            <div class="input-wrap has-icon">
                <input type="text" id="name_of_project" name="name_of_project" placeholder="e.g., Highway Expansion Phase 2" autocomplete="off">
                <span class="input-icon">🏗</span>
            </div>
            <p class="err-msg" id="err-name_of_project">⚠ Project name is required.</p>
            </div>

            <div class="field">
            <label for="project_location">Project Location <span class="req">*</span></label>
            <div class="input-wrap has-icon">
                <input type="text" id="project_location" name="project_location" placeholder="e.g., Davao City, Zone 3" autocomplete="off">
                <span class="input-icon">📍</span>
            </div>
            <p class="err-msg" id="err-project_location">⚠ Project location is required.</p>
            </div>

            <div class="field">
            <label for="for_office">For Office <span style="color:var(--muted);font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
            <div class="input-wrap has-icon">
                <input type="text" id="for_office" name="for_office" placeholder="e.g., Engineering Department">
                <span class="input-icon">🏢</span>
            </div>
            </div>

            <div class="field">
            <label for="from_requester">From / Requester <span style="color:var(--muted);font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
            <div class="input-wrap has-icon">
                <input type="text" id="from_requester" name="from_requester" placeholder="e.g., Project Manager">
                <span class="input-icon">👤</span>
            </div>
            </div>
        </div>

        <div class="nav-actions">
            <span></span>
            <button type="button" class="btn btn-primary" onclick="nextStep(1)">
            Continue <span>→</span>
            </button>
        </div>
        </div>

        <!-- STEP 2: Request Details -->
        <div class="section-panel" id="panel-2">
        <div class="panel-header">
            <div class="panel-tag green">📋 Step 2 of 4</div>
            <h2 class="panel-title">Request Details</h2>
            <p class="panel-sub">Describe the work being requested and schedule it.</p>
        </div>

        <div class="fields-grid">
            <div class="field">
            <label for="requested_by">Requested By <span class="req">*</span></label>
            <div class="input-wrap has-icon">
                <input type="text" id="requested_by" name="requested_by" value="John Dela Cruz" readonly>
                <span class="input-icon">👤</span>
                <span class="readonly-badge">Auto</span>
            </div>
            <p class="field-hint">Automatically filled with your account name.</p>
            </div>

            <div class="fields-grid two-col">
            <div class="field">
                <label for="requested_work_start_date">Start Date <span class="req">*</span></label>
                <div class="input-wrap has-icon">
                <input type="date" id="requested_work_start_date" name="requested_work_start_date">
                <span class="input-icon">📅</span>
                </div>
                <p class="err-msg" id="err-requested_work_start_date">⚠ Start date is required.</p>
            </div>
            <div class="field">
                <label for="requested_work_start_time">Start Time</label>
                <div class="input-wrap has-icon">
                <input type="time" id="requested_work_start_time" name="requested_work_start_time">
                <span class="input-icon">🕐</span>
                </div>
            </div>
            </div>

            <div class="field">
            <label for="description_of_work_requested">Description of Work <span class="req">*</span></label>
            <div class="input-wrap textarea-wrap has-icon">
                <textarea id="description_of_work_requested" name="description_of_work_requested" rows="5" placeholder="Provide a detailed description of the work to be performed..."></textarea>
                <span class="input-icon">📝</span>
            </div>
            <p class="err-msg" id="err-description_of_work_requested">⚠ Work description is required.</p>
            </div>
        </div>

        <div class="nav-actions">
            <button type="button" class="btn btn-ghost" onclick="prevStep(2)">← Back</button>
            <button type="button" class="btn btn-primary" onclick="nextStep(2)">Continue →</button>
        </div>
        </div>

        <!-- STEP 3: Pay Item Details & Submission -->
        <div class="section-panel" id="panel-3">
        <div class="panel-header">
            <div class="panel-tag orange">⚙️ Step 3 of 4</div>
            <h2 class="panel-title">Pay Item & Submission</h2>
            <p class="panel-sub">Specify the pay items, equipment, and submission details.</p>
        </div>

        <div class="fields-grid">
            <div class="fields-grid two-col">
            <div class="field">
                <label for="item_no">Item Number</label>
                <div class="input-wrap has-icon">
                <input type="text" id="item_no" name="item_no" placeholder="e.g., A-101">
                <span class="input-icon">#</span>
                </div>
            </div>
            <div class="field">
                <label for="equipment_to_be_used">Equipment to be Used</label>
                <div class="input-wrap has-icon">
                <input type="text" id="equipment_to_be_used" name="equipment_to_be_used" placeholder="e.g., Excavator, Roller">
                <span class="input-icon">🚧</span>
                </div>
            </div>
            </div>

            <div class="field">
            <label for="description_item">Item Description</label>
            <div class="input-wrap textarea-wrap has-icon">
                <textarea id="description_item" name="description" rows="3" placeholder="Brief description of the pay item..."></textarea>
                <span class="input-icon">📄</span>
            </div>
            </div>

            <div class="fields-grid two-col">
            <div class="field">
                <label for="estimated_quantity">Estimated Quantity</label>
                <div class="input-wrap has-icon">
                <input type="number" id="estimated_quantity" name="estimated_quantity" step="0.01" placeholder="0.00" min="0">
                <span class="input-icon">🔢</span>
                </div>
            </div>
            <div class="field">
                <label for="unit">Unit</label>
                <div class="input-wrap has-icon">
                <input type="text" id="unit" name="unit" placeholder="m, kg, hrs, cu.m">
                <span class="input-icon">📐</span>
                </div>
            </div>
            </div>

            <div class="field">
            <label for="contractor_name">Contractor Name</label>
            <div class="input-wrap has-icon">
                <input type="text" id="contractor_name" name="contractor_name" placeholder="e.g., XYZ Construction Corp.">
                <span class="input-icon">🏛</span>
            </div>
            </div>

            <div class="field">
            <label for="status">Status <span class="req">*</span></label>
            <div class="status-options" id="status-options">
                <label class="status-opt selected" data-val="pending">
                <input type="radio" name="status" value="pending" checked>
                <span class="status-dot" style="background:#ffaa4f;"></span>
                <span class="status-opt-label">Pending</span>
                </label>
                <label class="status-opt" data-val="in_progress">
                <input type="radio" name="status" value="in_progress">
                <span class="status-dot" style="background:#4f8dff;"></span>
                <span class="status-opt-label">In Progress</span>
                </label>
                <label class="status-opt" data-val="completed">
                <input type="radio" name="status" value="completed">
                <span class="status-dot" style="background:#00d4aa;"></span>
                <span class="status-opt-label">Completed</span>
                </label>
                <label class="status-opt" data-val="cancelled">
                <input type="radio" name="status" value="cancelled">
                <span class="status-dot" style="background:#ff6b6b;"></span>
                <span class="status-opt-label">Cancelled</span>
                </label>
            </div>
            <p class="err-msg" id="err-status">⚠ Please select a status.</p>
            </div>

            <div class="field">
            <label for="notes">Additional Notes</label>
            <div class="input-wrap textarea-wrap has-icon">
                <textarea id="notes" name="notes" rows="3" placeholder="Any additional information or special instructions..."></textarea>
                <span class="input-icon">💬</span>
            </div>
            </div>
        </div>

        <div class="nav-actions">
            <button type="button" class="btn btn-ghost" onclick="prevStep(3)">← Back</button>
            <button type="button" class="btn btn-primary" onclick="nextStep(3)">Review →</button>
        </div>
        </div>

        <!-- STEP 4: Review -->
        <div class="section-panel" id="panel-4">
        <div class="panel-header">
            <div class="panel-tag purple">✅ Step 4 of 4</div>
            <h2 class="panel-title">Review & Submit</h2>
            <p class="panel-sub">Double-check your details before submitting the work request.</p>
        </div>

        <div class="summary-grid" id="summary-grid">
            <!-- Filled by JS -->
        </div>

        <div class="nav-actions">
            <button type="button" class="btn btn-ghost" onclick="prevStep(4)">← Edit</button>
            <button type="button" class="btn btn-success" id="submit-btn" onclick="submitForm()">
            🚀 Submit Request
            </button>
        </div>
        </div>

    </form>

    <!-- Success screen -->
    <div class="success-screen" id="success-screen">
        <div class="success-icon">✓</div>
        <h2 class="success-title">Request Submitted!</h2>
        <p class="success-sub">Your work request has been successfully created and is now pending review.</p>
        <div class="req-id" id="req-id-display">WR-000000</div>
        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <button class="btn btn-ghost" onclick="resetForm()">Create Another</button>
        <button class="btn btn-primary" onclick="location.reload()">View My Requests</button>
        </div>
    </div>
    </main>

    <div class="floating-indicator" id="float-save">
    <span class="floating-dot"></span>
    Draft saved
    </div>

        <script>
            let currentStep = 1;
            const totalSteps = 4;

            // ---- Navigation ----
            function goToStep(n) {
            if (n > currentStep) return; // only allow going back via this
            showStep(n);
            }

            function nextStep(from) {
            if (!validateStep(from)) return;
            if (from === totalSteps - 1) {
                buildSummary();
            }
            showStep(from + 1);
            }

            function prevStep(from) {
            showStep(from - 1);
            }

            function showStep(n) {
            document.querySelectorAll('.section-panel').forEach(p => p.classList.remove('active'));
            document.getElementById(`panel-${n}`).classList.add('active');
            currentStep = n;
            updateStepIndicators();
            window.scrollTo({ top: 0, behavior: 'smooth' });
            autoSave();
            }

            function updateStepIndicators() {
            for (let i = 1; i <= totalSteps; i++) {
                const el = document.getElementById(`step-${i}`);
                el.classList.remove('active', 'done');
                if (i < currentStep) el.classList.add('done');
                else if (i === currentStep) el.classList.add('active');
                // Update inner number for done
                const num = el.querySelector('.step-num');
                if (i < currentStep) num.textContent = '✓';
                else num.textContent = i;
            }
            }

            // ---- Validation ----
            function validateStep(step) {
            let ok = true;
            const required = {
                1: ['name_of_project', 'project_location'],
                2: ['requested_work_start_date', 'description_of_work_requested'],
                3: [],
            };
            if (!required[step]) return true;

            // Clear previous errors
            required[step].forEach(id => {
                const el = document.getElementById(id);
                el.classList.remove('error');
                const err = document.getElementById(`err-${id}`);
                if (err) err.classList.remove('show');
            });

            required[step].forEach(id => {
                const el = document.getElementById(id);
                if (!el || !el.value.trim()) {
                el.classList.add('error');
                const err = document.getElementById(`err-${id}`);
                if (err) err.classList.add('show');
                ok = false;
                }
            });

            // Shake animation if invalid
            if (!ok) {
                const panel = document.getElementById(`panel-${step}`);
                panel.style.animation = 'none';
                setTimeout(() => panel.style.animation = '', 10);
            }

            return ok;
            }

            // ---- Status toggle ----
            document.querySelectorAll('.status-opt').forEach(opt => {
            opt.addEventListener('click', () => {
                document.querySelectorAll('.status-opt').forEach(o => o.classList.remove('selected'));
                opt.classList.add('selected');
            });
            });

            // ---- Summary builder ----
            function getVal(id) {
            const el = document.getElementById(id);
            return el ? el.value.trim() : '';
            }

            function getStatus() {
            const checked = document.querySelector('.status-opt.selected');
            return checked ? checked.dataset.val : '';
            }

            function buildSummary() {
            const sections = [
                {
                icon: '📁',
                title: 'Project Information',
                rows: [
                    { k: 'Project Name', v: getVal('name_of_project') },
                    { k: 'Location', v: getVal('project_location') },
                    { k: 'For Office', v: getVal('for_office') || '—' },
                    { k: 'From Requester', v: getVal('from_requester') || '—' },
                ]
                },
                {
                icon: '📋',
                title: 'Request Details',
                rows: [
                    { k: 'Requested By', v: getVal('requested_by') },
                    { k: 'Start Date', v: getVal('requested_work_start_date') || '—' },
                    { k: 'Start Time', v: getVal('requested_work_start_time') || '—' },
                    { k: 'Description', v: getVal('description_of_work_requested') },
                ]
                },
                {
                icon: '⚙️',
                title: 'Pay Item & Submission',
                rows: [
                    { k: 'Item No.', v: getVal('item_no') || '—' },
                    { k: 'Item Description', v: getVal('description_item') || '—' },
                    { k: 'Equipment', v: getVal('equipment_to_be_used') || '—' },
                    { k: 'Quantity', v: getVal('estimated_quantity') ? `${getVal('estimated_quantity')} ${getVal('unit')}` : '—' },
                    { k: 'Contractor', v: getVal('contractor_name') || '—' },
                    { k: 'Status', v: getStatus() || '—' },
                    { k: 'Notes', v: getVal('notes') || '—' },
                ]
                }
            ];

            const grid = document.getElementById('summary-grid');
            grid.innerHTML = sections.map(sec => `
                <div class="summary-section">
                <div class="summary-section-head">
                    <span>${sec.icon} ${sec.title}</span>
                </div>
                <div class="summary-rows">
                    ${sec.rows.map(r => `
                    <div class="summary-row">
                        <span class="summary-key">${r.k}</span>
                        <span class="summary-val ${r.v === '—' ? 'empty' : ''}">${r.v}</span>
                    </div>
                    `).join('')}
                </div>
                </div>
            `).join('');
            }

            // ---- Submit ----
            function submitForm() {
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.textContent = 'Submitting...';

            // Simulate async submit
            setTimeout(() => {
                document.getElementById('wr-form').style.display = 'none';
                const success = document.getElementById('success-screen');
                success.style.display = 'block';
                // Random request ID
                const id = 'WR-' + String(Math.floor(100000 + Math.random() * 900000));
                document.getElementById('req-id-display').textContent = id;
            }, 1400);
            }

            // ---- Auto-save draft ----
            let saveTimer;
            function autoSave() {
            clearTimeout(saveTimer);
            const ind = document.getElementById('float-save');
            ind.classList.add('show');
            saveTimer = setTimeout(() => ind.classList.remove('show'), 2000);
            }

            document.querySelectorAll('input, textarea, select').forEach(el => {
            el.addEventListener('input', () => {
                clearTimeout(el._t);
                el._t = setTimeout(autoSave, 800);
            });
            });

            // ---- Reset ----
            function resetForm() {
            document.getElementById('wr-form').style.display = '';
            document.getElementById('success-screen').style.display = 'none';
            document.getElementById('wr-form').reset();
            document.querySelectorAll('.status-opt').forEach(o => o.classList.remove('selected'));
            document.querySelector('.status-opt[data-val="pending"]').classList.add('selected');
            showStep(1);
            }

            // Set today as min date
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('requested_work_start_date').min = today;
        </script>
    </body>
</html>