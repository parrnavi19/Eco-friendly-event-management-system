-- ============================================
-- PASTE THIS ENTIRE FILE INTO PGADMIN QUERY TOOL
-- Database: eco_event
-- ============================================

DROP TABLE IF EXISTS eco_events;

CREATE TABLE eco_events (
    id SERIAL PRIMARY KEY,
    event_name VARCHAR(150) NOT NULL,
    event_type VARCHAR(80) NOT NULL,
    location VARCHAR(200) NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    organizer_name VARCHAR(100) NOT NULL,
    organizer_email VARCHAR(120) NOT NULL,
    organizer_phone VARCHAR(20),
    expected_attendees INT DEFAULT 0,
    carbon_offset_kg DECIMAL(10,2) DEFAULT 0.00,
    waste_reduction_goal VARCHAR(100),
    renewable_energy BOOLEAN DEFAULT FALSE,
    trees_planted INT DEFAULT 0,
    eco_score INT CHECK (eco_score BETWEEN 1 AND 100),
    description TEXT,
    status VARCHAR(30) DEFAULT 'Upcoming' CHECK (status IN ('Upcoming','Ongoing','Completed','Cancelled')),
    budget DECIMAL(12,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO eco_events (event_name,event_type,location,event_date,event_time,organizer_name,organizer_email,organizer_phone,expected_attendees,carbon_offset_kg,waste_reduction_goal,renewable_energy,trees_planted,eco_score,description,status,budget) VALUES
('Green Mumbai Marathon','Marathon','Marine Drive, Mumbai','2026-04-15','06:00:00','Priya Sharma','priya@green.com','9876543210',5000,250.50,'Zero plastic bottles',TRUE,100,92,'Annual eco-friendly marathon promoting sustainable living.','Upcoming',150000.00),
('Solar Tech Expo 2026','Exhibition','BKC Convention Centre, Mumbai','2026-05-20','09:00:00','Rahul Mehta','rahul@solar.com','9123456789',2000,180.00,'Solar-powered venue',TRUE,50,88,'Showcasing latest solar technology innovations.','Upcoming',500000.00),
('Mangrove Restoration Drive','Community Service','Thane Creek, Mumbai','2026-04-08','07:30:00','Anita Desai','anita@mangroves.org','9988776655',300,500.00,'Plant 500 mangroves',FALSE,500,98,'Community-led mangrove plantation drive.','Upcoming',25000.00),
('Organic Food Festival','Food Festival','Juhu Beach, Mumbai','2026-06-12','11:00:00','Vikram Joshi','vikram@organic.in','9567891234',8000,120.75,'Compostable packaging only',TRUE,30,85,'Celebrating organic farming and sustainable food.','Upcoming',200000.00),
('Climate Action Summit','Conference','IIT Bombay, Mumbai','2026-03-22','09:00:00','Dr. Sunita Rao','sunita@climate.in','9445566778',500,75.00,'Carbon neutral event',TRUE,20,95,'Summit for climate scientists and policy makers.','Completed',350000.00),
('Zero Waste Workshop','Workshop','Dadar Community Hall, Mumbai','2026-04-22','10:00:00','Meera Kulkarni','meera@zerowaste.com','9334455667',150,30.00,'Zero landfill waste',FALSE,10,90,'Hands-on composting and upcycling workshop.','Upcoming',15000.00),
('Cycle to Work Day','Awareness Campaign','MMRDA Ground, BKC','2026-04-25','08:00:00','Arjun Patil','arjun@cycle.com','9223344556',10000,800.00,'Replace 10000 car trips',FALSE,0,87,'City-wide cycling awareness campaign.','Upcoming',80000.00),
('Rainwater Harvesting Seminar','Seminar','MCA Auditorium, Churchgate','2026-05-05','14:00:00','Kavita Nair','kavita@water.org','9112233445',200,10.00,'Awareness for 200 residents',FALSE,5,78,'Technical seminar on rainwater harvesting.','Upcoming',20000.00);

CREATE OR REPLACE FUNCTION update_timestamp()
RETURNS TRIGGER AS $$
BEGIN NEW.updated_at = CURRENT_TIMESTAMP; RETURN NEW; END;
$$ language 'plpgsql';

CREATE TRIGGER update_eco_events_timestamp
    BEFORE UPDATE ON eco_events
    FOR EACH ROW EXECUTE FUNCTION update_timestamp();

SELECT id, event_name, event_type, event_date, eco_score, status FROM eco_events ORDER BY event_date;
