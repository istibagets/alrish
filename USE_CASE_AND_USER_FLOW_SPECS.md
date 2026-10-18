# Al Rish Al Khaleeji Enterprise Inventory & Smart Warranty Lifecycle Management System
## Formal Use Case Specification & User Flow Architecture

---

## 1. System Identity & Rephrased System Name

- **Official Name**: **Al Rish Al Khaleeji: Enterprise Inventory & Smart Warranty Lifecycle Management System**
- **Core Domain**: High-Precision Industrial Printing Machinery, CNC Routers, Co2 Lasers & Spare Parts Inventory Terminal with Integrated Machine Warranty Fleet Tracking.

---

## 2. Deliverables Generated & Ready for Use

| Deliverable | File Path | Description |
| :--- | :--- | :--- |
| **PowerPoint Presentation** | [`Al_Rish_System_Use_Cases_and_User_Flows.pptx`](file:///c:/laragon/www/alrish/Al_Rish_System_Use_Cases_and_User_Flows.pptx) | 16-slide master presentation matching the exact pedagogical and aesthetic style of the NEUST reference deck. |
| **Draw.io Use Case Diagram** | [`use_case_diagram.drawio.xml`](file:///c:/laragon/www/alrish/use_case_diagram.drawio.xml) | Native Draw.io XML for the complete UML Use Case Diagram (Actors, System Boundary, Ovals, `«include»`, `«extend»`). |
| **Draw.io User Flow Diagram** | [`user_flow_diagram.drawio.xml`](file:///c:/laragon/www/alrish/user_flow_diagram.drawio.xml) | Native Draw.io XML for POS Sales and Multi-Session Warranty Claim User Flows. |

---

## 3. How to Paste / Import into Draw.io (diagrams.net)

### Method A: Direct File Open (Easiest)
1. Go to **[https://app.diagrams.net](https://app.diagrams.net)**
2. Click **File** &rarr; **Open From** &rarr; **Device...**
3. Select [`use_case_diagram.drawio.xml`](file:///c:/laragon/www/alrish/use_case_diagram.drawio.xml) or [`user_flow_diagram.drawio.xml`](file:///c:/laragon/www/alrish/user_flow_diagram.drawio.xml).
4. The complete diagram will open with all vector shapes, colors, and layout intact.

### Method B: Paste XML Code Directly
1. In Draw.io, click **Arrange** &rarr; **Insert** &rarr; **Advanced** &rarr; **XML...**
2. Copy the entire contents of [`use_case_diagram.drawio.xml`](file:///c:/laragon/www/alrish/use_case_diagram.drawio.xml) or [`user_flow_diagram.drawio.xml`](file:///c:/laragon/www/alrish/user_flow_diagram.drawio.xml) and paste it into the dialog box.
3. Click **Import**.

---

## 4. Use Case Diagram Specification

### Actors
1. **Sales Counter Staff** *(Primary Human Actor)*: Front-line counter operator conducting customer sales, warranty part dispatches, and inventory lookups.
2. **Inventory Admin / Store Manager** *(Primary Human Actor - Privileged)*: Master catalog manager, category creator, stock discrepancy reconciler, and ledger auditor.
3. **Machinery Client** *(Secondary Human Role)*: Customer who purchases industrial machines and claims free parts under active warranty.
4. **Machinery Supplier** *(External Actor)*: Organization supplying wholesale machinery and spare parts.
5. **Realtime Broadcast Event Engine** *(System Secondary Actor)*: Instant multi-terminal cross-tab sync via `BroadcastChannel`.
6. **Low Stock Alert Monitor Daemon** *(System Secondary Actor)*: Automated daemon evaluating safety stock thresholds ($\le 5$ units).

### Use Cases (Verb-First, Goal-Oriented)
- **Process Multi-Line Customer Sale** *(Primary: Staff, Secondary: Client)*
  - `«include»` **Verify Real-Time Stock Balance**
  - `«extend»` **Grant Machine Warranty Period** *(Extension Point: Product Line is a Complete Machine Model Unit)*
  - `«extend»` **Auto-Suggest Registered Customer Profile** *(Extension Point: Customer name matches past records)*
- **Receive Supplier Machinery Restock** *(Primary: Staff / Admin, Secondary: Supplier)*
  - `«include»` **Verify Real-Time Stock Balance**
  - `«include»` **Broadcast Instant Cross-Terminal Mutation**
- **Claim Warranty & Order Free Replacement Part** *(Primary: Staff / Admin, Secondary: Client)*
  - `«include»` **Verify Active Machine Warranty Coverage**
  - `«include»` **Stock Out Part Under Warranty Protocol ($0 Sale)**
  - `«include»` **Broadcast Instant Cross-Terminal Mutation**
- **Audit Low Stock Fleet & Threshold Alerts** *(Primary: Staff / Admin, Secondary: Alert Daemon)*
- **Restructure Machinery Catalog & Quick-Edit** *(Primary: Admin)*
  - `«extend»` **Correct Live Stock Discrepancy Inline**
  - `«extend»` **Reassign Category Icons & Classifications**
- **Inspect Global Transaction Ledger & Audit Trail** *(Primary: Admin / Staff)*

---

## 5. User Flows Specification

### User Flow 1: POS Counter Multi-Product Sale & Warranty Registration
- **Task Statement**: *"A counter sales staff member wants to process a multi-line invoice containing a Complete UV Printer and 4 Ink Bottles for a client so that stock is deducted and a 12-month machine warranty is registered in the client fleet."*
- **Path**:
  1. `[Start]` Staff opens POS Counter Terminal with client waiting.
  2. `[Screen]` Enters client name &rarr; `[Decision]` Client in history?
     - **Yes**: `[Screen]` Client suggestions dropdown &rarr; Cashier clicks matching profile.
     - **No**: Enters new client name.
  3. `[Screen]` Quick Fast Product Search &rarr; Types model/part name &rarr; Selects item.
  4. `[Decision]` Is Complete Machine Unit?
     - **Yes**: Pre-fills 12 Months warranty.
     - **No**: Sets 0 Months warranty (spare part/consumable).
  5. `[Decision]` Stock on hand $\ge$ Qty?
     - **Yes**: Appends to POS multi-line ticket summary.
     - **No**: `[Screen]` Out of stock error &rarr; Adjusts quantity or selects substitute.
  6. `[Decision]` Add another line?
     - **Yes**: Loops back to search.
     - **No**: Clicks **Process Client Sale Ticket**.
  7. `[Screen]` Ticket confirmed, real-time broadcast fired, stock decremented, warranty recorded.
  8. `[End]` Customer receives receipt and machine warranty documentation.

---

### User Flow 2: Multi-Session Warranty Claim & Free Spare Part Dispatch
- **Task Statement**: *"A client (e.g. Steve) returns 4 months after purchase with a malfunctioning UV printhead; the staff member wants to locate his machine warranty record and order a $0 replacement spare part so that the customer's machine is restored without recording a revenue sale."*
- **Path**:
  1. `[Start]` Client arrives at service desk with machine issue months after sale.
  2. `[Screen]` Staff opens Warranty Management View.
  3. `[Screen]` Searches Client Name & expands Client Fleet Accordion.
  4. `[Screen]` System displays purchased machine unit with active countdown (`8 Months Active`).
  5. `[Decision]` Is Warranty Still Active?
     - **Yes (Active)**: Displays emerald active badge &rarr; Staff clicks **Order Parts**.
     - **No (Expired)**: Displays red expired badge &rarr; Redirects to Standard Paid Sale (Flow 1).
  6. `[Screen]` POS Counter opens in dedicated **WARRANTY CLAIM MODE** (Client & Machine pre-bound).
  7. `[Screen]` Staff selects damaged part (e.g., *i3200 Printhead*).
  8. `[Screen]` Submits Claim &rarr; Warehouse stock decrements by 1; revenue recorded as $0 (Warranty Claim Outbound).
  9. `[End]` Client receives free replacement part; audit trail updated.

---

### User Flow 3: Dashboard Direct Catalog Quick-Edit by Admin
- **Task Statement**: *"An inventory admin auditing the Detailed Stock Balance spots a discrepancy in 'Stepper Motor' and wants to immediately jump to Catalog Admin to edit the product line name and stock balance."*
- **Path**:
  1. `[Start]` Admin reviews category cards on Dashboard.
  2. `[Screen]` Hovers over product line name (displays interactive edit pencil) & clicks.
  3. `[Action]` System validates Admin role, clears any active search filters on Catalog Admin, and switches tab to `tab-admin`.
  4. `[Action]` Smoothly scrolls to the targeted category card and row.
  5. `[Screen]` Row pulses with glowing ring highlight (`ring-2 ring-brand-500`).
  6. `[Action]` Product line name input auto-focuses and selects text for immediate editing.
  7. `[End]` Admin edits and saves changes with zero search friction.
