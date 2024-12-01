-- Blog Posts Table
CREATE TABLE blog_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    excerpt TEXT,
    featured_image VARCHAR(255),
    author_id INT,
    category VARCHAR(50),
    tags JSON,
    status ENUM('draft', 'published') DEFAULT 'draft',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id)
);

-- Blog Comments Table
CREATE TABLE blog_comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT,
    user_id INT,
    content TEXT NOT NULL,
    status ENUM('pending', 'approved', 'spam') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES blog_posts(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert sample blog posts
INSERT INTO blog_posts (title, slug, content, excerpt, category, tags, status) VALUES
(
    '5 Essential Tips for Building an Emergency Fund',
    '5-essential-tips-emergency-fund',
    '# Why You Need an Emergency Fund

An emergency fund is your financial safety net, protecting you from unexpected expenses and providing peace of mind. Here are five essential tips to help you build and maintain one:

## 1. Start Small, Think Big

Begin with a goal of saving $1,000, then work your way up to 3-6 months of living expenses. Even small weekly contributions add up over time.

## 2. Automate Your Savings

Set up automatic transfers from your checking to your emergency fund account. This "set it and forget it" approach ensures consistent saving.

## 3. Keep It Separate

Maintain your emergency fund in a separate high-yield savings account to avoid the temptation of spending it and earn some interest.

## 4. Define "Emergency"

Be clear about what constitutes an emergency. Job loss, medical expenses, and critical home repairs qualify; vacation spending doesn''t.

## 5. Regularly Review and Adjust

As your life circumstances change, so should your emergency fund target. Review it annually and adjust based on your current expenses.',
    'Learn how to build and maintain an emergency fund with these practical tips that anyone can follow.',
    'Personal Finance',
    '["savings", "emergency fund", "financial planning", "budgeting"]',
    'published'
),
(
    'Understanding Market Volatility: A Beginner''s Guide',
    'understanding-market-volatility',
    '# Making Sense of Market Ups and Downs

Market volatility can be intimidating for new investors. Here''s what you need to know to stay calm during market turbulence.

## What Is Market Volatility?

Market volatility refers to the rate at which stock prices move up and down. It''s a natural part of investing, not necessarily a sign of trouble.

## Why Markets Become Volatile

- Economic changes
- Political events
- Company earnings
- Global events
- Investor sentiment

## How to Handle Volatility

1. Maintain a long-term perspective
2. Diversify your portfolio
3. Avoid emotional decisions
4. Consider dollar-cost averaging
5. Keep some cash reserves

Remember: Volatility creates both risks and opportunities. The key is staying informed and maintaining your investment strategy.',
    'Learn how to understand and handle market volatility with confidence.',
    'Investing',
    '["investing", "market volatility", "stock market", "risk management"]',
    'published'
),
(
    'The Digital Revolution in Personal Finance',
    'digital-revolution-personal-finance',
    '# How Technology Is Transforming Money Management

The way we manage money is changing dramatically with new financial technologies. Here''s what you need to know about the digital finance revolution.

## Key Innovations

### Mobile Banking
24/7 access to your accounts, mobile check deposits, and instant transfers

### Automated Investing
Robo-advisors making professional investment management accessible to everyone

### Digital Payments
Contactless payments, digital wallets, and peer-to-peer money transfers

### Budgeting Apps
Real-time tracking, categorization, and insights into spending habits

## Benefits of Digital Finance

- Convenience
- Lower costs
- Better insights
- Improved security
- Greater accessibility

## Looking Ahead

The future of finance is increasingly digital. Stay informed about new tools and features that can help you manage your money more effectively.',
    'Explore how digital technologies are revolutionizing personal finance management.',
    'Technology',
    '["fintech", "digital banking", "mobile payments", "financial apps"]',
    'published'
),
(
    'Maximizing Your Credit Score: Expert Strategies',
    'maximizing-credit-score',
    '# Building and Maintaining a Strong Credit Score

Your credit score impacts everything from loan rates to rental applications. Here are expert strategies to maximize it.

## Understanding Credit Score Factors

1. Payment History (35%)
2. Credit Utilization (30%)
3. Length of Credit History (15%)
4. Credit Mix (10%)
5. New Credit (10%)

## Action Steps

### Short-term Improvements
- Pay bills on time
- Reduce credit utilization
- Dispute errors
- Become an authorized user

### Long-term Strategy
- Maintain old accounts
- Mix credit types
- Limit new applications
- Monitor your score

Remember: Good credit is built over time through consistent responsible behavior.',
    'Learn proven strategies to improve and maintain a high credit score.',
    'Credit',
    '["credit score", "credit cards", "personal finance", "loans"]',
    'published'
),
(
    'Sustainable Investing: Profit with Purpose',
    'sustainable-investing-guide',
    '# Guide to Environmental, Social, and Governance (ESG) Investing

Sustainable investing lets you grow your wealth while supporting positive change. Here''s how to get started.

## What Is ESG Investing?

ESG investing considers:
- Environmental impact
- Social responsibility
- Corporate governance

## Benefits of Sustainable Investing

- Competitive returns
- Lower risks
- Positive impact
- Future-focused portfolio

## How to Start

1. Define your values
2. Research ESG funds
3. Check fund ratings
4. Monitor performance
5. Stay informed

## Common Myths Debunked

- "ESG investing means lower returns" (False)
- "It''s just a trend" (False)
- "Only for wealthy investors" (False)

The future of investing is sustainable. Start aligning your portfolio with your values today.',
    'Discover how to invest sustainably while maintaining strong returns.',
    'Investing',
    '["ESG", "sustainable investing", "green finance", "responsible investing"]',
    'published'
); 